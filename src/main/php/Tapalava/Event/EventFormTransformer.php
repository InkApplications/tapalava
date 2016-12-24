<?php

namespace Tapalava\Event;

use DateInterval;
use DatePeriod;
use DateTime;

/**
 * Transforms Schedule models to and from view-form appropriate array data.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class EventFormTransformer
{
    /**
     * Change view array data into a Schedule model.
     *
     * @param array $viewData The array data from a view form.
     * @return Event The equivalent model object matching the given array.
     */
    public function fromView(array $viewData, $scheduleId = null): Event
    {
        $id = $viewData['id'] ?? null;

        $name = $viewData['name'] ?? null;
        $description = $viewData['description'] ?? null;
        $banner = $viewData['banner'] ?? null;
        $category = $viewData['category'] ?? null;
        $room = $viewData['room'] ?? null;

        $start = isset($viewData['start']) ? new DateTime($viewData['start']) : null;
        $end = isset($viewData['end']) ? new DateTime($viewData['end']) : null;

        $rawTags = $viewData['tags'] ?? null;
        $tags = empty($rawTags) ? [] : explode(',', $rawTags);
        array_walk($tags, [$this, 'cleanTag']);

        $rawHosts = $viewData['hosts'] ?? null;
        $hosts = empty($rawHosts) ? [] : explode(',', $rawHosts);
        array_walk($hosts, [$this, 'cleanTag']);


        return new Event($id, $scheduleId, $name, $start, $end, $category, $tags, $room, $hosts, $description, $banner);
    }

    /**
     * Change a Schedule model into an array of view data.
     *
     * @param Event $model Model data to be changed into a view-form data.
     * @return array The equivalent form view-data matching the model.
     */
    public function toView(Event $model): array
    {
        return [
            'id' => $model->getId(),
            'scheduleId' => $model->getScheduleId(),
            'name' => $model->getName(),
            'start' => $model->getStart() == null ? null : $model->getStart()->format(DateTime::RFC3339),
            'end' => $model->getEnd() == null ? null : $model->getEnd()->format(DateTime::RFC3339),
            'category' => $model->getCategory(),
            'tags' => null == $model->getTags() ? null : implode(',', $model->getTags()),
            'room' => $model->getRoom(),
            'hosts' => null == $model->getHosts() ? null : implode(',', $model->getHosts()),
            'description' => $model->getDescription(),
            'banner' => $model->getBanner(),
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
}
