<?php

namespace Tapalava\Schedule;

use DateInterval;
use DatePeriod;
use DateTime;

/**
 * Transforms Schedule models to and from view-form appropriate array data.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class ScheduleFormTransformer
{
    /**
     * Change view array data into a Schedule model.
     *
     * @param array $viewData The array data from a view form.
     * @return Schedule The equivalent model object matching the given array.
     */
    public function fromView(array $viewData): Schedule
    {
        $id = $viewData['id'] ?? null;
        $name = $viewData['name'] ?? null;
        $description = $viewData['description'] ?? null;
        $banner = $viewData['banner'] ?? null;
        $location = $viewData['location'] ?? null;

        $startDate = $viewData['startDate'] ?? null;
        $endDate = $viewData['endDate'] ?? null;
        $days = $this->dateTransform($startDate, $endDate);

        $rawTags = $viewData['tags'] ?? null;
        $tags = empty($rawTags) ? [] : explode(',', $rawTags);
        array_walk($tags, [$this, 'cleanTag']);


        return new Schedule($id, $name, $days, $description, $banner, $location, $tags);
    }

    /**
     * Change a Schedule model into an array of view data.
     *
     * @param Schedule $model Model data to be changed into a view-form data.
     * @return array The equivalent form view-data matching the model.
     */
    public function toView(Schedule $model): array
    {
        $startDate = null;
        $endDate = null;

        if (count($model->getDays()) != 0) {
            $startDate = $model->getDays()[0]->format('Y-m-d');
            $endDate = $model->getDays()[count($model->getDays()) - 1]->format('Y-m-d');
        }
        return [
            'id' => $model->getId(),
            'name' => $model->getName(),
            'description' => $model->getDescription(),
            'banner' => $model->getBanner(),
            'location' => $model->getLocation(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tags' => null == $model->getTags() ? null : implode(',', $model->getTags()),

        ];
    }

    /**
     * Normalize any insignificant formatting discrepancies in tags.
     *
     * @param string $tag the tag to be cleaned.
     * @return string The clean version of the tag.
     */
    protected function cleanTag($tag)
    {
        return trim($tag);
    }

    /**
     * Get a DateTime object for every day in a range (inclusive)
     *
     * @param string|null $startDate The date range to start at.
     * @param string|null $endDate The date to end at.
     * @return array|null An array of days in the range, null if unacceptable range.
     */
    protected function dateTransform($startDate = null, $endDate = null)
    {
        if (null == $startDate || null == $endDate) {
            return null;
        }

        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);
        $days = [];

        foreach ($period as $date) {
            $days[] = $date;
        }

        $days[] = $endDate;

        return $days;
    }
}
