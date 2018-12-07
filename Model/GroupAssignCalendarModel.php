<?php

namespace Kanboard\Plugin\Group_assign\Model;

use DateTime;
use Kanboard\Model\TimezoneModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ColorModel;
use Kanboard\Core\Base;

/**
 * Group_assign Calendar Model
 *
 * @package  Kanboard\Plugin\Group_assign
 * @author   Craig Crosby
 */
class GroupAssignCalendarModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'tasks';
    /**
     * Get query to fetch all users
     *
     * @access public
     * @param  integer $group_id
     * @return \PicoDb\Table
     */
    public function getUserCalendarEvents($user_id, $start, $end)
    {
         $tasks = $this->db->table(self::TABLE)
            ->eq('user_id', $user_id)
            ->gte('date_due', strtotime($start))
            ->lte('date_due', strtotime($end))
            ->neq('is_active', 0)
            ->findAll();
            
         $events = array();

         foreach ($tasks as $task) {
                  
         $startDate = new DateTime();
         $startDate->setTimestamp($task['date_started']);
         
         $endDate = new DateTime();
         $endDate->setTimestamp($task['date_due']);
         
         if ($startDate == 0) { $startDate = $endDate; }
         
         $allDay = $startDate == $endDate && $endDate->format('Hi') == '0000';
         $format = $allDay ? 'Y-m-d' : 'Y-m-d\TH:i:s';
            
            $events[] = array(
                'timezoneParam' => $this->timezoneModel->getCurrentTimezone(),
                'id' => $task['id'],
                'title' => t('#%d', $task['id']).' '.$task['title'],
                'backgroundColor' => $this->colorModel->getBackgroundColor($task['color_id']),
                'borderColor' => $this->colorModel->getBorderColor($task['color_id']),
                'textColor' => 'black',
                'url' => $this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
                'start' => $startDate->format($format),
                'end' => $endDate->format($format),
                'editable' => $allDay,
                'allday' => $allDay,
            );
         }
         
         return $events;
    }
    
    public function getProjectCalendarEvents($project_id, $start, $end)
    {
        $alltasks = $this->taskFinderModel->getAllIds($project_id);
        
        $tasks = array();
        
        foreach ($alltasks as $lonetask) {
        
         $foundtasks = $this->db->table(self::TABLE)
            ->eq('task_id', $lonetask['id'])
            ->gte('date_due', strtotime($start))
            ->lte('date_due', strtotime($end))
            ->neq('is_active', 0)
            ->findAll();
                   
         $tasks = array_merge($tasks, $foundtasks);
                        
        }
            
         $events = array();

         foreach ($tasks as $task) {
                  
         $startDate = new DateTime();
         $startDate->setTimestamp($task['date_started']);
         
         $endDate = new DateTime();
         $endDate->setTimestamp($task['date_due']);
         
         if ($startDate == 0) { $startDate = $endDate; }
         
         $allDay = $startDate == $endDate && $endDate->format('Hi') == '0000';
         $format = $allDay ? 'Y-m-d' : 'Y-m-d\TH:i:s';
            
            $events[] = array(
                'timezoneParam' => $this->timezoneModel->getCurrentTimezone(),
                'id' => $task['id'],
                'title' => t('#%d', $task['id']).' '.$task['title'],
                'backgroundColor' => $this->colorModel->getBackgroundColor($task['color_id']),
                'borderColor' => $this->colorModel->getBorderColor($task['color_id']),
                'textColor' => 'black',
                'url' => $this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
                'start' => $startDate->format($format),
                'end' => $endDate->format($format),
                'editable' => $allDay,
                'allday' => $allDay,
            );
         }
         
         return $events;
    }
}
