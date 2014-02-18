<?php

   namespace Adservice\UtilBundle\Entity;

   use Doctrine\ORM\Mapping as ORM;
   use Symfony\Component\HttpFoundation\File\UploadedFile;
   use Symfony\Component\Validator\Constraints as Assert;

  /**
   * Adservice\UtilBundle\Entity\Document
   * @ORM\Entity(repositoryClass="Adservice\UtilBundle\Entity\DocumentRepository")
   * @ORM\HasLifecycleCallbacks
   * @ORM\Table(name="document")
   */
  class Document
 {    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string $name
     */
    private $name;
    /**
    * @var string $post
    * 
    * @ORM\ManyToOne(targetEntity="\Adservice\TicketBundle\Entity\Post")
    */
    public $post;    
    /**
    *  @ORM\Column(type="string", length=255, nullable=true)
    */
    public $path;
    /**
    * @Assert\File(maxSize="6000000")
    */
    public $file;
    
    /**
    * Get id
    *
    * @return integer
    */
    public function getId()
    {
       return $this->id;
    }
    
    /**
    * Set name
    *
    * @param string $name
    */
    public function setName($name)
    {
       $this->name = $name;
    }
    
    /**
    * Get name
    *
    * @return string
    */
    public function getName()
    {
       return $this->name;
    }
    
    /**
     * Set post
     *
     * @param \Adservice\TicketBundle\Entity\Post $post
     */
    public function setPost(\Adservice\TicketBundle\Entity\Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get post
     *
     * @return string 
     */
    public function getPost()
    {
        return $this->post;
    }
    
    /**
    * Set path
    *
    * @param integer $path
    */
    public function setPath($path)
    {
       $this->path = $path;
    }

    /**
    * Get path
    *
    * @return integer
    */
    public function getPath()
    {
       return $this->path;
    }
    
    /**
     * Sets file.
     *
     * @param  $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    //Encuentra la url absoluta y cambia el principio a localhost
    public function getImgPath()
    {
       return null === $this->path ? null : str_replace("/var/www", "", $this->getUploadRootDir()).'/'.$this->path;
    }
    
    public function getAbsolutePath()
    {
       return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
       return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir( )
    {
        $id_post = $this->getPost()->getId();
        $id_ticket = $this->getPost()->getTicket()->getId();
        
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        $url = 'uploads/tickets/'.$id_ticket.'/'.$id_post;

        return $url;
    }
    /**
    * @ORM\PrePersist()
    */
    public function preUpload()
    {
       if (null !== $this->file) {
           // do whatever you want to generate a unique name
           $this->path = uniqid().'.'.$this->file->guessExtension();
       }
    }

    /**
    * @ORM\PostPersist()
    */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }
        $this->file->move($this->getUploadRootDir(), $this->path);
  
        $this->file = null;
    }

    /**
    * @ORM\PostRemove()
    */
    public function removeUpload()
    {
       if ($file = $this->getAbsolutePath()) {
           unlink($file);
       }
    }
}