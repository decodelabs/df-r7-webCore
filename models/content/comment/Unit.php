<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\content\comment;

use df;
use df\core;
use df\apex;
use df\axis;
use df\mesh;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;

class Unit extends axis\unit\Table
{
    const ORDERABLE_FIELDS = [
        'title', 'date', 'owner', 'isLive'
    ];

    const DEFAULT_ORDER = 'date ASC';

    protected function createSchema($schema)
    {
        $schema->addPrimaryField('id', 'Guid');

        $schema->addIndexedField('topic', 'EntityLocator');
        $schema->addIndexedField('date', 'Date:Time');
        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('body', 'Text', 'huge');
        $schema->addField('format', 'Text', 64);

        $schema->addField('root', 'ManyToOne', 'comment', 'replyTree')
            ->isNullable(true);
        $schema->addField('replyTree', 'OneToMany', 'comment', 'root');
        $schema->addField('inReplyTo', 'ManyToOne', 'comment', 'replies')
            ->isNullable(true);
        $schema->addField('replies', 'OneToMany', 'comment', 'inReplyTo');

        $schema->addIndexedField('isLive', 'Boolean')
            ->setDefaultValue(false);
    }


    public function postFor($entityLocator, $body, $user=null, $inReplyTo=null, $format='SimpleTags')
    {
        if ($user == null) {
            $user = Disciple::getId();
        }

        $comment = $this->newRecord([
            'topic' => $this->_normalizeItemLocator($entityLocator),
            'date' => 'now',
            'owner' => $user,
            'body' => $body,
            'format' => $format,
            'inReplyTo' => $inReplyTo,
            'isLive' => true
        ]);

        $comment->save();
        return $this;
    }

    public function countFor($entityLocator, $includeHidden=false)
    {
        $query = $this->select()->where('topic', '=', $this->_normalizeItemLocator($entityLocator));

        if (!$includeHidden) {
            $query->where('isLive', '=', true);
        }

        return $query->count();
    }

    public function deleteFor($entityLocator)
    {
        $this->delete()
            ->where('topic', '=', $this->_normalizeItemLocator($entityLocator))
            ->execute();

        return $this;
    }

    protected function _normalizeItemLocator($locator)
    {
        if (is_array($locator)) {
            foreach ($locator as $i => $value) {
                $locator[$i] = $this->_normalizeItemLocator($value);
            }

            return $locator;
        }

        $locator = mesh\entity\Locator::factory($locator);

        if (!$locator->getId()) {
            throw Exceptional::{'df/mesh/entity/InvalidArgument'}(
                'Locator does not have an id'
            );
        }

        return $locator;
    }



    public function deleteRecord(namespace\Record $comment)
    {
        if (!$comment['#root']) {
            $this->delete()
                ->where('root', '=', $comment)
                ->execute();
        } else {
            $deleteIndex[] = $comment['id'];

            $tree = $comment['root']->replyTree->select('id', 'inReplyTo')
                ->orderBy('date ASC')
                ->toList('id', 'inReplyTo');

            foreach ($tree as $id => $inReplyTo) {
                if (in_array($inReplyTo, $deleteIndex)) {
                    $deleteIndex[] = $id;
                }
            }

            $this->delete()
                ->where('id', 'in', $deleteIndex)
                ->execute();
        }


        $comment->delete();
        return $this;
    }
}
