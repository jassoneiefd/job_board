<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Applicant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


use Doctrine\ORM\EntityManagerInterface;

use App\Entity\JobOffer;
use App\Form\ApplicationType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class OfferController extends AbstractController

{
    #[Route('/', name: 'offer_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $offers = $entityManager->getRepository(JobOffer::class)
            ->findAll();

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
        ]);
    }
    
    //#[Route('job_offer/{id}/apply', name: 'offer_apply')]

    public function apply(int $id, Request $request, EntityManagerInterface $entityManager)
    {
        

        if (!($offer = $entityManager->getRepository(JobOffer::class)
            ->find($id))) {
            
            throw new NotFoundHttpException();
             
        }
        
        $applicant = new Applicant();
        $form = $this->createForm(ApplicationType::class, $applicant);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

           $entityManager->persist($applicant);
           $entityManager->flush();
           //return new Response('<h1> Your application was received</h1>');
           $this->addFlash('success', 'Your application has been received' );
           return $this->redirectToRoute('offer_index');


        }

        return $this->render('offer/apply.html.twig', [
            'offer' => $offer, // Puedes pasar la oferta como un parámetro opcional
            'form' => $form->createView(),
        ]);
    }

        
    #[IsGranted('ROLE_COMPANY_OWNER')]
    #[Route('/company/', name: 'company_offers_index', methods: ['GET'])]
    /**
    
    * @return Response
    */

    public function companyOffers() : Response
    {
        
        $user = $this->getUser();
        $company = $user->getCompany();

        if (!$company){
           
            return $this->redirectToRoute('company_create');

        }

        return $this->render('offer/company.html.twig', [
            'offers' => $company ? $company->getJobOffers() : [], 
        ]);
    }
     







}
