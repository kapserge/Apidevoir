<?php

namespace App\Controller;

use App\Entity\Creditcard;
use App\Entity\Master;
use App\Repository\CreditcardRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;
class CreditcardController extends FOSRestController
{
    private $creditcardRepository;
    private $em;
    public function __construct(CreditcardRepository $creditcardRepository, EntityManagerInterface $em)
    {
        $this->creditcardRepository = $creditcardRepository;
        $this->em = $em;
    }
    private function MasterDroitM(Master $master)
    {
        if ($this->getUser() === $master || in_array("ROLE_ADMIN",$this->getUser()->getRoles()) ) {
            $return = true;
        } else {
            $return = false;
        }
        return $return;
    }
    private function MasterDroit()
    {
        if (in_array("ROLE_ADMIN",$this->getUser()->getRoles()) ) {
            $return = true;
        } else {
            $return = false;
        }
        return $return;
    }
    private function PostError($validationErrors){
        $error = array("error :");
        /** @var ConstraintViolationListInterface $validationErrors */
        /** @var ConstraintViolation $constraintViolation */
        foreach ($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            array_push($error,$propertyPath.' => '.$message);
        }
        return $error;
    }
    /**
     * @SWG\Parameter(
     *     name="AUTH-TOKEN",
     *     in="header",
     *     type="string",
     *     description="Api Token"
     * )
     * @SWG\Response(response=200, description="")
     * @SWG\Tag(name="Creditcard")
     * @Rest\View(serializerGroups={"Creditcard"})
     */
    public function getCreditcardsAction()
    {
        if($this->getUser() !== null )
        {
            if ($this->MasterDroit()) {
                return $this->view($this->creditcardRepository->findAll());
            }
            return $this->view('Not Logged for this user or not an Admin', 403);
        } else {
            return $this->view('Not Logged', 401);
        }
    }
    /**
     * @SWG\Response(response=200, description="")
     * @SWG\Tag(name="Creditcard")
     * @Rest\View(serializerGroups={"Creditcard"})
     *
     */
    public function getCreditcardAction(Creditcard $creditcard)
    {
     
                return $this->view($creditcard);
    }
    /**
     * @SWG\Response(response=200, description="")
     * @SWG\Tag(name="Creditcard")
     * @Rest\View(serializerGroups={"Creditcard"})
     * @Rest\Post("/Creditcard")
     * @ParamConverter("creditcard", converter="fos_rest.request_body")
     */
    public function postCreditcardsAction(Creditcard $creditcard, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        ///empecher anonymous de créer une carte
        $validationErrors = $validator->validate($creditcard);
        if(!($validationErrors->count() > 0) ){
            $this->em->persist($creditcard);
            $this->em->flush();
            return $this->view($creditcard,200);
        } else {
            return $this->view($this->PostError($validationErrors),400);
        }
    }
    /**
     *  @SWG\Parameter(
     *     name="AUTH-TOKEN",
     *     in="header",
     *     type="string",
     *     description="Api Token"
     * )
     * @SWG\Response(response=200, description="")
     * @SWG\Tag(name="Creditcard")
     * @Rest\View(serializerGroups={"Creditcard"})
     */
    public function putCreditcardAction(Request $request, $id, ValidatorInterface $validator)
    {
        $users = $this->creditcardRepository->find($id);
        if($users === null){
            return $this->view('User does note existe', 404);
        }
        // dump($this->getUser());die;
        if ($id == $this->getUser()->getId() || $this->MasterDroit()) {
            /** @var Master $us */
            $us = $this->creditcardRepository->find($id);
            /* $firstname = $request->get('firstname');
             $lastname = $request->get('lastname');
             $email = $request->get('email');
             $company = $request->get('birthday');
             if (isset($firstname)) {
                 $us->setFirstname($firstname);
             }
             if (isset($lastname)) {
                 $us->setLastname($lastname);
             }
             if (isset($email)) {
                 $us->setEmail($email);
             }
             if (isset($company)) {
                 $us->setCompany($company);
             }*/
            $this->em->persist($us);
            $validationErrors = $validator->validate($us);
            if(!($validationErrors->count() > 0) ) {
                $this->em->flush();
                return $this->view("ok",200);
            } else {
                return $this->view($this->PostError($validationErrors),401);
            }
        } else {
            return $this->view('Not the same user or tu n as pas les droits',401);
        }
    }
    /**
     * @SWG\Parameter(
     *     name="AUTH-TOKEN",
     *     in="header",
     *     type="string",
     *     description="Api Token"
     * )
     * @SWG\Response(response=200, description="")
     * @SWG\Tag(name="Creditcard")
     * @Rest\View(serializerGroups={"Creditcard"})
     */
    public function deleteCreditcardAction($id)
    {
        /** @var Master $us */
        $users = $this->creditcardRepository->findBy(["id"=>$id]);
        if($users === []){
            return $this->view('User does note existe', 404);
        }
        if($this->getUser() !== null ) {
            $us = $this->creditcardRepository->find($id);
            if ($us === $this->getUser() || $this->MasterDroit()) {
                $this->em->remove($us);
                $this->em->flush();
            } else {
                return $this->view('Not the same user or tu n as pas les droits',401);
            }
        } else {
            return $this->view('Not Logged', 401);
        }
    }
}