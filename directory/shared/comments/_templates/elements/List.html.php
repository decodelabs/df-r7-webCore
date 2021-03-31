<?php
use df\mesh;
use DecodeLabs\Tagged as Html;

$paginator = null;

if ($this->getSlot('paginate', true)) {
    echo $paginator = (string)$this->html->paginator($commentList);
}

echo Html::list($commentList, 'ol.w.articleList', 'li > article', function ($comment, $el) {
    $displayAsTree = $this['displayAsTree'];
    $hash = 'comment-'.$comment->getUniqueId();
    $el->setId($hash);
    $redir = clone $this->context->request;
    $redir->setFragment($hash);

    // Header
    yield Html::{'header'}([
        // Avatar
        $this->html->image(
                $this->avatar->getAvatarUrl($comment['owner']['id'], 50),
                'avatar'
            )
            ->setStyles('float: left; margin-right: 0.6em;'),

        // By
        Html::{'h3'}(
             $this->apex->component('~admin/users/clients/UserLink', $comment['owner'])
                ->setIcon(null)
        ),


        Html::{'p'}([
            // Time
            Html::$time->since($comment['date']),

            // In reply to
            $displayAsTree || !($inReplyTo = $comment['inReplyTo']) ? null :
            $this->html->_(' in reply to %u%', [
                    '%u%' => $this->html->link(
                    '#'.$inReplyTo->getUniqueId(),
                    $inReplyTo['owner']['fullName']
                )
                ->setIcon('user')
                ->setDisposition('transitive')
            ]),

            // Hash link
            ' ',
            $this->html->link('#'.$hash, '#')
                ->setTitle($this->_('Direct link to this comment'))
                ->setDisposition('transitive')
        ])
    ]);

    // Body
    yield Html::{'section'}($this->html->convert($comment['body'], $comment['format']));

    // Footer
    if ($this->getSlot('showFooter', true)) {
        yield Html::{'footer'}([
            $this->html->link(
                    $this->uri('~/comments/reply?comment='.$comment['id'], $redir),
                    $this->_('Reply')
                )
                ->setIcon('comment')
                ->setDisposition('positive'),

            $this->html->link(
                    $this->uri('~/comments/edit?comment='.$comment['id'], $redir),
                    $this->_('Edit')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri('~/comments/delete?comment='.$comment['id'], $redir),
                    $this->_('Delete')
                )
                ->setIcon('delete'),
        ]);
    }

    if ($displayAsTree) {
        $replies = $comment->getPopulatedTreeReplies();

        if (!empty($replies)) {
            yield $this->apex->template('~/comments/#/elements/List.html', [
                'commentList' => $replies,
                'paginate' => false,
                'displayAsTree' => true
            ]);
        }
    }
});

echo $paginator;


if ($this->getSlot('showForm', false) && isset($entity)) {
    $locator = mesh\entity\Locator::factory($entity);
    echo $this->apex->form('~/comments/add?entity='.$locator);
}
