<?php

namespace Jdvorak23\Visits\Model;

use Nette\SmartObject;
use Nette\Utils\DateTime;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

/**
 * @property-read DateTime $firstDay
 * @property DateTime $today
 */
class VisitManager
{
    use SmartObject;

    protected DateTime $propFirstDay;
    protected DateTime $propToday;

    public function __construct(protected Explorer $database)
    {
    }

//---------------------------------------- visits -----------------------------------------
    public function addVisit(string $ip, string $page): void
    {
        $isExcludedIp = (bool) $this->database->table('ip')
            ->where('`ip` = ?', $ip)
            ->count();
        if($isExcludedIp)
            return;
        $this->getTable()->insert([
                'ip' => $ip,
                'page' => $page
        ]);
    }

    public function getTotal(): VisitsData
    {
        return $this->getVisitsData();
    }
    public function getYear(): VisitsData
    {
        $firstDayOfYear = $this->today->modifyClone('first day of january this year');
        return $this->getVisitsData($firstDayOfYear);
    }
    public function getMonth(): VisitsData
    {
        $firstDayOfMonth = $this->today->modifyClone('first day of this month');
        return $this->getVisitsData($firstDayOfMonth);
    }
    public function getWeek(): VisitsData
    {
        $firstDay = $this->today->modifyClone('-6 days');
        return $this->getVisitsData($firstDay);
    }

    public function getVisitsData(?DateTime $from = null, ?DateTime $to = null): VisitsData
    {
        return new VisitsData($this->getAccesses($from, $to),
            $this->getUips($from, $to),
            $this->getTotalDays($from, $to));
    }
//---------------------------------------- pages -----------------------------------------
    public function getPagesVisits(?DateTime $from = null, ?DateTime $to = null): PagesData
    {
        $pages = $this->getDatesSelection($from, $to)
            ->select('`page`')
            ->group('page')
            ->order('`count` DESC');
        return new PagesData($pages);
    }
//---------------------------------------- ips -----------------------------------------
    public function getIps(): Selection
    {
        return $this->database->table('ip')
            ->select('`ip`, `note`');
    }
    public function addIp(string $ip, string $note = ''): void
    {
        $this->database->table('ip')
            ->insert(['ip' => $ip, 'note' => $note]);
        $this->getTable()
            ->where('`ip` = ?', $ip)
            ->delete();
    }
    public function removeIp(string $ip): void
    {
        $this->database->table('ip')
            ->where('`ip` = ?', $ip)
            ->delete();
    }
//---------------------------------------- magic properties getters/setters -----------------------------------------
    protected function getFirstDay(): DateTime
    {
        if(isset($this->propFirstDay))
            return $this->propFirstDay;
        $firstAccess = $this->getTable()
            ->select('`date`')
            ->order('`date` ASC')
            ->limit(1)
            ->fetch();
        $this->propFirstDay = $firstAccess ? $firstAccess->date : $this->today;
        return $this->propFirstDay;
    }
    protected function getToday(): DateTime
    {
        if(isset($this->propToday))
            return $this->propToday;
        $this->propToday = new DateTime();
        return $this->propToday;
    }
    protected function setToday(DateTime $today): void
    {
        $this->propToday = $today;
    }
//---------------------------------------- protected -----------------------------------------
    protected function getTable(): Selection
    {
        return $this->database->table('visit');
    }
    protected function getAccesses(?DateTime $from = null, ?DateTime $to = null): int
    {
        return $this->getDatesSelection($from, $to)
            ->fetch()
            ->count;
    }

    protected function getUips(?DateTime $from = null, ?DateTime $to = null): int
    {
        return $this->getDatesSelection($from, $to, true)
            ->fetch()
            ->count;
    }
    protected function getDatesSelection(?DateTime $from = null, ?DateTime $to = null, bool $distinctIp = false): Selection
    {
        $from = $from ?? $this->firstDay;
        $to = $to ?? $this->today;
        $selection = $this->getTable()
            ->where('`date` >= ?', $this->getPreviousMidnight($from))
            ->where('`date` <= ?', $this->getNextMidnight($to));
        if($distinctIp)
            $selection->select('COUNT(DISTINCT `ip`) AS `count`');
        else
            $selection->select('COUNT(*) AS `count`');
        return $selection;
    }

    protected function getTotalDays(?DateTime $from = null, ?DateTime $to = null): int
    {
        $from = $from ?? $this->firstDay;
        $to = $to ?? $this->today;
        return (int) $this->getPreviousMidnight($from)
            ->diff($this->getNextMidnight($to))
            ->format('%a');
    }
    protected function getNextMidnight(DateTime $date): DateTime
    {
        return $date->modifyClone('+1 day')->setTime(0,0);
    }
    protected function getPreviousMidnight(DateTime $date): DateTime
    {
        return (clone $date)->setTime(0,0);
    }



//---------------------------------




}