<?php

namespace Sandbox\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="articles")
 * @ORM\Entity
 */
class Article
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @Assert\NotNull
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    protected $body;

    /**
     * @ORM\Column(name="media", type="string", length=255, nullable=true)
     */
    protected $media;

    /**
     * @Assert\Range(
     *      min = "0"
     * )
     *
     * @ORM\Column(name="cost", type="decimal", scale=2, nullable=true)
     */
    protected $cost;

    /**
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="articles")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="Author")
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id")
     */
    protected $editor;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="articles")
     * @ORM\JoinTable(name="articles_tags")
     */
    protected $tags;

    /**
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="Link", mappedBy="article", cascade={"persist"}, orphanRemoval=true)
     */
    protected $links;

    /**
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    protected $date;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->links = new ArrayCollection();
        $this->date = new \DateTime();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function setEditor($editor)
    {
        $this->editor = $editor;
    }

    public function getEditor()
    {
        return $this->editor;
    }

    public function setMedia($media)
    {
        $this->media = $media;
    }

    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @return Link[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    public function addLink(Link $link)
    {
        $link->setArticle($this);
        $this->links->add($link);
    }

    public function removeLink(Link $link)
    {
        $this->links->removeElement($link);
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    public function getCost()
    {
        return $this->cost;
    }
}
