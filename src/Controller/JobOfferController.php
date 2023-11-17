<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\ApplicationType;
use App\Entity\Applicant;
use App\Entity\JobOffer;
use App\Form\JobOfferType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
//#[Route('/', name: 'offer_index')]
#[Route('/job/offer')]
class JobOfferController extends AbstractController
{
    #[IsGranted('ROLE_COMPANY_OWNER')]

    #[Route('/', name: 'app_job_offer_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();

        if (!$company){
           
            return $this->redirectToRoute('company_create');

        }
        
        
        
        
        return $this->render('job_offer/index.html.twig', [
            'job_offers' => $company->getJobOffers(),
        ]);
    }

    #[IsGranted('ROLE_COMPANY_OWNER')]
    #[Route('/new', name: 'app_job_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jobOffer = new JobOffer();
        $jobOffer->setCompany($this->getUser()->getCompany());
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobOffer);
            $entityManager->flush();

            return $this->redirectToRoute('app_job_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job_offer/new.html.twig', [
            'job_offer' => $jobOffer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_job_offer_show', methods: ['GET'])]
    public function show(JobOffer $jobOffer): Response
    {
        return $this->render('job_offer/show.html.twig', [
            'job_offer' => $jobOffer,
        ]);
    }

    #[Security("(is_granted('ROLE_COMPANY_OWNER') and jobOffer.getCompany() == user.getCompany")]
    #[Route('/{id}/edit', name: 'app_job_offer_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, JobOffer $jobOffer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_job_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job_offer/edit.html.twig', [
            'job_offer' => $jobOffer,
            'form' => $form,
        ]);
    }
    #[Security("(is_granted('ROLE_COMPANY_OWNER') and jobOffer.getCompany() == user.getCompany")]
    #[Route('/{id}', name: 'app_job_offer_delete', methods: ['POST'])]
    public function delete(Request $request, JobOffer $jobOffer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobOffer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($jobOffer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_job_offer_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('job_offer/{id}/apply', name: 'offer_apply')]
    /** 
    *@return Response 
    *@param Request $request
    *@param EntityManagerInterface $entityManager
    */
    public function apply(int $id, Request $request, EntityManagerInterface $entityManager,MailerInterface $mailer)
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

           $email=
                (new Email())
                    ->from('botsitoelmejorsito1@gmail.com')
                    ->to('jassoneiefd@gmail.com')
                    ->subject('New application recieved!')
                    ->html('<p>' . $applicant->getName() . ' applied for ' . $offer->getTittle() . '</p>' . '<p>please contact to ' . $applicant->getEmail() . '<p>')
            ;

            $dsn = 'gmail://botsitoelmejorsito1@gmail.com:dfmalfenvfaggpwe@default';

            $transport = Transport ::fromDsn($dsn);

            $mail= new Mailer($transport);

            $mail->send($email);





           //return new Response('<h1> Your application was received</h1>');
           $this->addFlash('success', 'Your application has been received' );
           return $this->redirectToRoute('offer_index');


        }

        return $this->render('offer/apply.html.twig', [
            'offer' => $offer, // Puedes pasar la oferta como un parámetro opcional
            'form' => $form->createView(),
        ]);
    }
}
