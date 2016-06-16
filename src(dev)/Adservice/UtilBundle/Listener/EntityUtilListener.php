<?php 
//namespace Adservice\UtilBundle\Listener;
//
//use Doctrine\Common\Persistence\Mapping\ClassMetadata;
//use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
//use Doctrine\ORM\Event\LifecycleEventArgs;
//
//use Doctrine\Common\EventSubscriber,
//    Doctrine\ORM\Event\OnFlushEventArgs,
//    Doctrine\ORM\Events;
//
//class EntityUtilListener {
//
//    /**
//     * @var callable
//     */
//    private $userCallable;
//
//    /**
//     * @var mixed
//     */
//    private $user;
//
//    /**
//     * userEntity name
//     */
//    private $userEntity;
//
//    /**
//     * @param callable
//     * @param string $userEntity
//     */
//    public function __construct(ClassAnalyzer $classAnalyzer, $isRecursive, callable $userCallable = null, $userEntity = null) {
//        parent::__construct($classAnalyzer, $isRecursive);
//
//        $this->userCallable = $userCallable;
//        $this->userEntity = $userEntity;
//    }
//
//    /**
//     * Stores the current user into createdBy and updatedBy properties
//     *
//     * @param LifecycleEventArgs $eventArgs
//     */
//    public function prePersist(LifecycleEventArgs $eventArgs) {
//        $em = $eventArgs->getEntityManager();
//        $uow = $em->getUnitOfWork();
//        $entity = $eventArgs->getEntity();
//
//        $classMetadata = $em->getClassMetadata(get_class($entity));
//        if ($this->isEntitySupported($classMetadata->reflClass, true)) {
//            if (!$entity->getCreatedBy()) {
//                $user = $this->getUser();
//                if ($this->isValidUser($user)) {
//                    $entity->setCreatedBy($user);
//
//                    $uow->propertyChanged($entity, 'createdBy', null, $user);
//                    $uow->scheduleExtraUpdate($entity, [
//                        'createdBy' => [null, $user],
//                    ]);
//                }
//            }
//            if (!$entity->getUpdatedBy()) {
//                $user = $this->getUser();
//                if ($this->isValidUser($user)) {
//                    $entity->setUpdatedBy($user);
//                    $uow->propertyChanged($entity, 'updatedBy', null, $user);
//
//                    $uow->scheduleExtraUpdate($entity, [
//                        'updatedBy' => [null, $user],
//                    ]);
//                }
//            }
//        }
//    }
//
//}
