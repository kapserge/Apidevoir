<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Master;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
class CompanyController extends FOSRestController
{
    private $companyRepository;
    private $em;
    public function __construct(CompanyRepository $companyRepository, EntityManagerInterface $em)
    {
        $this->companyRepository = $companyRepository;
        $this->em = $em;
    }
    private function MasterDroitMaster(Master $master)
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
     * @SWG\Tag(name="Company")
     * @Rest\View(serializerGroups={"Company"})
     */
    public function getCompanysAction()
    {
        if($this->getUser() !== null )
        {
            if ($this->MasterDroit()) {
                return $this->view($this->companyRepository->findAll());
            }
            return $this->view('Not Logged for this user or not an Admin', 403);
        } else {
            return $this->view('Not Logged', 401);
        }
    }
    /**
     * @SWG\Response(response=200, description="")
     * @SWG\Tag(name="Company")
     * @Rest\View(serializerGroups={"Company"})
     *
     */
    public function getCompanyAction(Company $company)
    {
       /* if($this->getUser() !== null ) {
            if ($this->MasterDroitMaster($company->getMaster())) {*/
                return $this->view($company);
           /* }
            return $this->view('Not Logged for this user or not an Admin', 403);
        } else {
            return $this->view('Not Logged', 401);
        }*/
    }
    /**
     * @SWG\Response(response=200, description="")
     * @SWG\Tag(name="Company")
     * @Rest\View(serializerGroups={"Company"})
     * @Rest\Post("/Company")
     * @ParamConverter("company", converter="fos_rest.request_body")
     */
    public function postCompanysAction(Company $company, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $validationErrors = $validator->validate($company);
        if(!($validationErrors->count() > 0) ){
            $this->em->persist($company);
            $this->em->flush();
            return $this->view($company,200);
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
     * @SWG\Tag(name="Company")
     * @Rest\View(serializerGroups={"Company"})
     */
    public function putCompanyAction(Request $request, $id, ValidatorInterface $validator)
    {
        $users = $this->companyRepository->find($id);
        if($users === null){
            return $this->view('User does note existe', 404);
        }
        // dump($this->getUser());die;
        if ($id == $this->getUser()->getId() || $this->MasterDroit()) {
            /** @var Master $us */
            $us = $this->companyRepository->find($id);
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
     * @SWG\Tag(name="Company")
     * @Rest\View(serializerGroups={"Company"})
     */
    public function deleteCompanyAction($id)
    {
        /** @var Master $us */
        $users = $this->companyRepository->findBy(["id"=>$id]);
        if($users === []){
            return $this->view('User does note existe', 404);
        }
        if($this->getUser() !== null ) {
            $us = $this->companyRepository->find($id);
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