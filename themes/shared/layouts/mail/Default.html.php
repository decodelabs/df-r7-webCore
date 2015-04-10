<?php
$this->view
  ->shouldRenderBase(false)
  ->setTitle($this->getSlot('title'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $this->esc($this->view->getTitle()); ?></title>
</head>
<body bgcolor="#EEEEEE" style="padding: 0; font-size: 14px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #EEEEEE;">
        <tr>
            <td align="center" valign="top" style="padding: 25px;">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="border: 1px #E4E4E4 solid; background: #FFF; box-shadow: 0 0 3px #DDD; border-radius: 5px;">
                    <tr>
                        <td style="font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; color: #555; padding: 20px 20px 35px">
                            <?php echo $this->renderInnerContent(); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>