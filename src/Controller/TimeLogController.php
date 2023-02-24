<?php

namespace App\Controller;

use App\Entity\TimeLog;
use App\Form\TimeLogType;
use App\Repository\TimeLogRepository;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Time;

class TimeLogController extends AbstractController
{
    /**
     * @Route("/", name="time_log_index")
     */
    public function index(TimeLogRepository $timeLogRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p.name AS project_name, t.id AS time_log_id, t.start_time, t.end_time, t.duration FROM App\Entity\TimeLog t LEFT JOIN  App\Entity\Project p WITH p.id = t.project ORDER BY p.name, t.start_time');
        $results = $query->getResult();
        return $this->render('time_log/index.html.twig',[
            'time_logs' => $results,
        ]);
    }

    /**
     * @Route("/timelog/new", name="time_log_new", methods={"GET","POST"})
     */

    public function new(Request $request):Response
    {
        $timeLog = new TimeLog();
        $form = $this-> createForm(TimeLogType::class,$timeLog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($timeLog);
            $entityManager->flush();
            return $this->redirectToRoute('time_log_index');
        }
        return $this->render('time_log/new.html.twig', [
            'time_log' => $timeLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/timelog/{id}/edit", name="time_log_edit", methods={"GET","POST"})
     */

    public function edit(Request $request, TimeLog $timeLog):Response
    {
        $form = $this->createForm(TimeLogType::class,$timeLog);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('time_log_index');
        }

        return $this->render('time_log/edit.html.twig',[
            'time_log' => $timeLog,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route ("timelog/{id}",name="time_log_delete",methods={"DELETE"})
     */
    public function delete(Request $request, TimeLog $timeLog):Response
    {
        if ($this->isCsrfTokenValid('delete'.$timeLog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($timeLog);
            $entityManager->flush();
        }
        return $this->redirectToRoute('time_log_index');
    }

    /**
     * @Route("timelog/evaluation", name="time_log_evaluation")
     */
    public function evaluation(TimeLogRepository $timeLogRepository)
    {
        // Query the database to get time logs grouped by day and month

        $timeLog = $timeLogRepository->findAll();
        $em = $this->getDoctrine()->getManager();
        $logsByMonth = $em->createQuery('SELECT MONTH(e.start_time) as month, SUM(e.duration) as total_duration FROM App\Entity\TimeLog e GROUP BY month');
        $monthlyData = $logsByMonth->getResult();

        /*$logsByDay = $em->createQuery('SELECT DATE(e.start_time) as date, SUM(e.duration) as total_duration FROM App\Entity\TimeLog e GROUP BY date');
        $dailyData = $logsByMonth->getResult();*/

        return $this->render('time_log/evaluation.html.twig', [
            'monthlyData' => $monthlyData,
            'time_logs' => $timeLog,
        ]);


    }
    /**
     * @Route("timelog/export", name="time_log_export_time")
     */

    public function export(TimeLogRepository $timeLogRepository)
    {
        //Route in config/routes.yaml
        $timeLogs = $timeLogRepository->findAll();
        $csv = Writer::createFromString('');
        $csv->insertOne(['Start Time', 'End Time', 'Duration']);

        foreach ($timeLogs as $timeLog) {
            $csv->insertOne([$timeLog->getStartTime()->format('Y-m-d H:i:s'), $timeLog->getEndTime()->format('Y-m-d H:i:s'), $timeLog->getDuration()]);
        }
        //Getting months data
        $em = $this->getDoctrine()->getManager();
        $logsByMonth = $em->createQuery('SELECT MONTH(e.start_time) as month, SUM(e.duration) as total_duration FROM App\Entity\TimeLog e GROUP BY month');
        $monthlyData = $logsByMonth->getResult();

        foreach($monthlyData as $key => $data ){
            $monthName = date("F", mktime(0, 0, 0, $data['month'], 1));
            $csv->insertOne([$monthName ,  $data['total_duration']]);
        }


        $response = new Response($csv->getContent(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="time-logs.csv"',
        ]);

        return $response;
    }



}
