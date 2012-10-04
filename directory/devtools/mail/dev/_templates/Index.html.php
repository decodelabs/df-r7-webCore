<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~devtools/mail/dev/delete-all', true),
                $this->_('Delete all mail')
            )
            ->setIcon('delete')
            ->addAccessLock('axis://mail/DevMail#delete'),

        '|',

        $this->html->backLink()
    );


echo $this->html->collectionList($this['mailList'])
    ->setErrorMessage($this->_('There are currently no emails to view'))

    // Subject
    ->addField('subject', function($mail) {
        return $this->html->link(
                $this->uri->request('~devtools/mail/dev/details?mail='.$mail['id'], true),
                $this->format->shorten($mail['subject'], 50)
            )
            ->setIcon('mail');
    })

    // From
    ->addField('from', function($mail) {
        if(!$from = $mail->getFromAddress()) {
            return null;
        }

        return $this->html->link(
                $this->uri->mailto($from->getAddress()),
                $from->getAddress()
            )
            ->setIcon('user')
            ->setDescription($from->getName());
    })

    // To
    ->addField('to', function($mail) {
        $addresses = $mail->getToAddresses();
        $first = array_shift($addresses);

        $output = [
            $this->html->link(
                    $this->uri->mailto($first->getAddress()),
                    $first->getAddress()
                )
                ->setIcon('user')
                ->setDescription($first->getName())
        ];

        if(!empty($addresses)) {
            $output[] = $this->html->string(
                '<span class="state-lowPriority">'.$this->esc($this->_(
                    ' and %c% more',
                    ['%c%' => count($addresses)]
                )).'</span>'
            );
        }

        return $output;
    })

    // Date
    ->addField('date', $this->_('Sent'), function($mail) {
        return $this->format->userDateTime($mail['date'], 'medium');
    })

    // Is private
    ->addField('isPrivate', $this->_('Private'), function($mail) {
        return $this->html->lockIcon($mail['isPrivate']);
    })

    // Actions
    ->addField('actions', function($mail) {
        return [
            $this->html->link(
                    $this->uri->request('~devtools/mail/dev/delete?mail='.$mail['id'], true),
                    $this->_('Delete')
                )
                ->setIcon('delete')
                ->addAccessLock('axis://mail/DevMail#delete')
        ];
    })
    ;