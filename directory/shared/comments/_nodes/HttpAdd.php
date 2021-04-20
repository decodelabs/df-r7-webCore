<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\comments\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;

use DecodeLabs\Disciple;

class HttpAdd extends arch\node\Form
{
    protected $_comment;

    protected function init()
    {
        $this->data->fetchEntityForAction(
            $this->location->query['entity'],
            'comment'
        );

        $this->_comment = $this->data->newRecord('axis://content/Comment');
    }

    protected function getInstanceId()
    {
        return $this->location->query['entity'];
    }

    protected function setDefaultValues()
    {
        $this->values->isLive = true;
    }

    protected function createUi()
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Add your comment'));

        $this->_renderHistory($fs);

        if ($this->isRenderingInline()) {
            $form->setAction($this->uri($this->location, $this->request));
            $fs->addHidden('isInline', true);

            // Live
            $fs->addHidden('isLive', true);

            // Body
            $fs->push(
                $this->html->textarea('body', $this->values->body)
                    ->isRequired(true)
            );

            // Buttons
            $fs->addButtonArea(
                $this->html->saveEventButton('save', $this->_('Post comment')),
                $this->html->resetEventButton()
            );
        } else {
            // Live
            if ($this->_comment->isNew()) {
                $fs->addHidden('isLive', true);
            } else {
                $fs->addField()->push(
                    $this->html->checkbox(
                            'isLive',
                            $this->values->isLive,
                            $this->_('This comment is live and readable')
                        )
                );
            }

            // Body
            $fs->addField($this->_('Body'))->push(
                $this->html->textarea('body', $this->values->body)
                    ->isRequired(true)
            );

            // Buttons
            $fs->addDefaultButtonGroup();
        }
    }

    protected function _renderHistory($fs)
    {
    }

    protected function onSaveEvent()
    {
        $this->data->newValidator()
            // Live
            ->addRequiredField('isLive', 'boolean')
                ->setDefaultValue(true)

            // Body
            ->addRequiredField('body', 'text')

            ->validate($this->values)
            ->applyTo($this->_comment);

        return $this->complete(function () {
            $this->_comment->format = 'SimpleTags';
            $this->_prepareRecord();

            $this->_comment->save();
            $isYours = $this->_comment['#owner'] == Disciple::getId();

            // TODO: send notification?

            $this->comms->flashSaveSuccess('comment',
                $isYours ?
                    $this->_('Your comment has been successfully saved'):
                    $this->_('The comment has been successfully saved')
            );
        });
    }

    protected function onResetEvent()
    {
        $inline = $this->values['isInline'];
        $output = parent::onResetEvent();

        if ($inline) {
            return $this->http->defaultRedirect();
        }

        return $output;
    }

    protected function _prepareRecord()
    {
        $this->_comment->topic = $this->request['entity'];
        $this->_comment->owner = Disciple::getId();
    }
}
