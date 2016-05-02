<?php

namespace Tapalava\AuthenticationBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Basic implementation of the FOSUserBundle Model.
 *
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
