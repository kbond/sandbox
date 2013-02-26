<?php

namespace Sandbox\AppBundle\Services;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("app.mailer", public=false)
 * @DI\Tag("zenstruck_dashboard.service", attributes = {"alias" = "mailer"})
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MailerService
{
    public function getUnreadMessages()
    {
        return rand(1, 20);
    }
}