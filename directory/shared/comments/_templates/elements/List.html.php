<?php
use df\mesh;

$paginator = null;

if($this->getSlot('paginate', true)) {
    echo $paginator = (string)$this->html->paginator($this['commentList']);
}

echo $this->html->articleList($this['commentList'], function($comment, $context) {
    $hash = 'comment-'.$comment->getUniqueId();
    $context->getCellTag()->setId($hash);
    $redir = clone $this->context->request;
    $redir->setFragment($hash);

    $output = [
        // Header
        $this->html('header', [
            // Avatar
            $this->html->image(
                    $this->avatar->getAvatarUrl($comment['owner']['id'], 50), 
                    'avatar'
                )
                ->setStyles('float: left; margin-right: 0.6em;'),

            // By
            $this->html('h3', 
                 $this->apex->component('~admin/users/clients/UserLink', $comment['owner'])
                    ->setIcon(null)
            ),

            
            $this->html('p', [
                // Time
                $this->html->timeFromNow($comment['date']),

                // In reply to
                $this['displayAsTree'] || !($inReplyTo = $comment['inReplyTo']) ? null :
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
        ]),

        // Body
        $this->html('section', $this->html->convert($comment['body'], $comment['format']))
    ];

    // Footer
    if($this->getSlot('showFooter', true)) {
        $output[] = $this->html('footer', [
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

    if($this['displayAsTree']) {
        $replies = $comment->getPopulatedTreeReplies();

        if(!empty($replies)) {
            $output[] = $this->html->template('~/comments/#/elements/List.html', [
                'commentList' => $replies,
                'paginate' => false,
                'displayAsTree' => true
            ]);
        }
    }

    return $output;
});

echo $paginator;


if($this->getSlot('showForm', false) && $this->hasSlot('entity')) {
    $locator = mesh\entity\Locator::factory($this['entity']);
    echo $this->apex->form('~/comments/add?entity='.$locator);
}