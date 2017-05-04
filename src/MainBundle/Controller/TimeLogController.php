<?php

namespace MainBundle\Controller;

use MainBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use \DateTime;
use \Doctrine\Common\Util\Debug as MyDebug;

Debug::enable();

ErrorHandler::register();

ExceptionHandler::register();

class TimeLogController extends Controller
{
    /**
     * @Route("/log/{username}/{year}/{week}")
     */
    public function indexAction($username, $year, $week)
    {   
        $return['uri'] = '/stt';
        
        $user = $this->get('security.context')->getToken()->getUser();
        $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
        
        $em = $this->getDoctrine()->getManager();
        $return["username"] = $username;
        
        $employeeObject = $em->getRepository('MainBundle:Employee')->findOneBy(array('username' => $username));
        #MyDebug::Dump($employeeObject);die;

        /* Keine Berechtigung beim Versuch die Seite eines anderen Mitarbeiters zu sehen (ausser man ist Admin) */
        if ($user->getUsername() !== $employeeObject->getUsername() && !$isAdmin ) {
             return $this->render('MainBundle:Security:nopermission.html.twig', array(
                'data' => $return
            ));
        }

        $return['employee'] = $employeeObject;
        
        $return["year"] = (int)$year;
        $return["week"] = (int)$week;
        
        $return["weekArray"] =$this->getStartAndEndDate($week, $year);
        
        
        $return['startYear'] = 2016; 
        $return["currentYear"] = (int)date("Y");
        $return["currentWeek"] = (int)date("W");

        /* Endwoche für die Auswahl wird bestimmt (wenn das Jahr in der Vergangenheit liegt, dann maximalzahl der Wochen, sonst nur bis zu aktuellen Woche) */
        if ($return['year'] < $return['currentYear']) {
            $return["endWeek"] = $this->getIsoWeeksInYear($return['year']);
        } else {
            $return["endWeek"] = $return["currentWeek"];
        }

        /* Es wird sichergestellt das im aktuellen Jahr nur bis zu aktuellen Woche ausgewählt werden darf */
        
        if ($return['year'] == $return['currentYear'] && $return['week'] > $return['currentWeek']) {
           return $this->redirect($return['uri'].'/'.'log'.'/'.$username.'/'.$year.'/'.$return['currentWeek']);
        }

        $projectsRes = $em->getRepository('MainBundle:Project')->findBy(array(), array('sort' => 'asc', 'name' => 'asc'));
        foreach ($projectsRes as $project) {
            $return["projects"][$project->getId()] = $project;
        }

        $timeLogsRepository = $em->getRepository('MainBundle:TimeLog');
        $commentsRepository = $em->getRepository('MainBundle:Comment');
        $weekLockRepository = $em->getRepository('MainBundle:WeekLock');

        $isWeekLocked = $weekLockRepository->findOneBy(array( 
                                                            'year' => $year, 
                                                            'week' => $week, 
                                                            'username' => $username)
                                                            );
        
        /* Daten Speichern vvvvvvvvvvvvvvvv */        
        if (isset($_POST['submit']) && !empty($_POST['submit']) && !$isWeekLocked) {
            
            foreach ($return["projects"] as $project) {
                $projectId = $project->getId();
                /* Zeiten Speichern vvvvvvvvvvv*/
                for ($day = 1; $day <= 5; $day++) {                    
                    if (null != $_POST[$projectId.'_'.$day]) {
                        $newTimeLog = str_replace(',', '.', $_POST[$projectId.'_'.$day]);
                        if ($timeLog = $timeLogsRepository->findOneBy(array( 
                                                                    'year' => $year, 
                                                                    'week' => $week, 
                                                                    'project' => $projectId, 
                                                                    'username' => $username,
                                                                    'day' => $day )
                        )) { 
                            if ($timeLog->getTimelog() != $_POST[$projectId.'_'.$day]) {
                                $timeLog->setTimelog($newTimeLog);                                
                            }               
                        } 
                        else {
                            $timeLog = new Entity\TimeLog();
                            $timeLog->setYear($year);
                            $timeLog->setWeek($week);
                            $timeLog->setDay($day);
                            $timeLog->setProject($project);
                            $timeLog->setUsername($username);
                            $timeLog->setTimelog($newTimeLog);
                        }
                        $em->persist($timeLog);
                        $em->flush();
                    }
                }
                /* Zeiten Speichern ENDE ^^^^^^^^*/

                /* Kommentare Speichern vvvvvvvvvvvvv*/
                if (null != $_POST['comment_'.$projectId]) {
                    if($comment = $commentsRepository->findOneBy(array(
                                                                'year' => $year,
                                                                'week' => $week,
                                                                'project' => $projectId,
                                                                'username' => $username
                    ))){
                        if($comment->getComment() != $_POST['comment_'.$projectId]) {
                            $comment->setComment($_POST['comment_'.$projectId]);
                        }
                    } else {
                        $comment = new Entity\Comment();
                        $comment->setYear($year);
                        $comment->setWeek($week);
                        $comment->setProject($project);
                        $comment->setUsername($username);
                        $comment->setComment($_POST['comment_'.$projectId]);
                    }
                    $em->persist($comment);
                    $em->flush();
                }
                /* Kommentare Speichern ENDE ^^^^^^^^*/
            }
            /* Woche abschließen vvvvvvvv */
            if (isset($_POST['weeklock']) && $_POST['weeklock'] == 'set') {
                $weekLock = new Entity\WeekLock();
                $weekLock->setYear($year);
                $weekLock->setWeek($week);
                $weekLock->setUsername($username);
                $em->persist($weekLock);
                $em->flush();
            }
            /* Woche abschließen ENDE ^^ */
        }        
        /* Daten Speichern ENDE ^^^^^^^^^^ */
        
        $isWeekLocked = $weekLockRepository->findOneBy(array( 
                                                            'year' => $year, 
                                                            'week' => $week, 
                                                            'username' => $username)
                                                            );
        if ($isWeekLocked) {
            $return['weeklock'] = true;
        }
        $dayTimes[1] = 0;
        $dayTimes[2] = 0;
        $dayTimes[3] = 0;
        $dayTimes[4] = 0;
        $dayTimes[5] = 0;
        if (!empty($return['projects'])) {
            foreach ($return["projects"] as $key => $project) {
                $projectId = $project->getId();
                for ($day = 1; $day <= 5; $day++) {
                    if ($timeLog = $timeLogsRepository->findOneBy(array( 
                                                                'year' => $year, 
                                                                'week' => $week, 
                                                                'project' => $projectId, 
                                                                'username' => $username,
                                                                'day' => $day )
                    )) { 
                        $return["projects"][$projectId]->times[$day] = $timeLog->getTimelog();
                        $dayHours = $timeLog->getTimelog();
                        
                        $dayTimes[$day] = $dayTimes[$day] + $dayHours;              
                    } else {
                        $return["projects"][$projectId]->times[$day] = '';
                    }
                }

                if($comment = $commentsRepository->findOneBy(array(
                                                                'year' => $year,
                                                                'week' => $week,
                                                                'project' => $projectId,
                                                                'username' => $username
                ))){
                    $return["comments"][$projectId] = $comment->getComment();
                } else {
                    $return["comments"][$projectId] = '';
                }           
            }
        }

        $return['daytimes'] = $dayTimes;
        #MyDebug::Dump($dayTimes);die;
        return $this->render('MainBundle:TimeLog:timelog.html.twig', array(
            'data' => $return
        ));
    }

    function getStartAndEndDate($week, $year) {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $ret['start'] = $dto->format('d.m.Y');
        $dto->modify('+4 days');
        $ret['end'] = $dto->format('d.m.Y');
        return $ret;
    }

    function getIsoWeeksInYear($year) {
        $date = new DateTime;
        $date->setISODate($year, 53);
        return ($date->format("W") === "53" ? 53 : 52);
    }
}