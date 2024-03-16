<?php
class AI {
    private $api_key;

    public function __construct() {
        include $_SERVER['DOCUMENT_ROOT'].'/../openai_key.php';
        $this->api_key = OPENAI_API_KEY;
    }

    private function request($requestBody) {
        // https://platform.openai.com/docs/quickstart?context=curl
        // https://platform.openai.com/docs/api-reference/chat
        // https://www.php.net/manual/en/book.curl.php

        $ch = curl_init("https://api.openai.com/v1/chat/completions");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $this->api_key"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);
        curl_close($ch);

        $completion = json_decode($data, true);

        // TODO: Better error handling
        if (isset($completion['error'])) {
            return false;
        }

        return $completion['choices'][0]['message']['content'];
    }

    public function adjustSchedule($courses, $form, $schedule) {
        $courseData = json_encode($courses);
        $priorCoursesData = implode(", ", $form->courses);
        $scheduleData = json_encode($schedule);
        $notes = $form->notes;
        $perQuarter = $form->coursesPerQuarter;

        $requestBody = [
            "model" => "gpt-3.5-turbo-0125",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a tool for optimizing academic schedules. You will take an existing JSON schedule and do your absolute best to update the schedule so that it respects the student's preferences perfectly. The field names in the updated schedule should be structured like the original version. You may add new quarters ONLY if you need more space to correct the schedule. If a quarter is to be removed, the JSON object for that quarter should contain no classes, rather than being deleted entirely.\n\nNEVER include the same class more than once, and NEVER include classes that the student has already taken. Classes should NEVER be added or removed from the schedule, only moved to different locations.\n\nIf a class has prerequisites, the student should complete ALL prerequisites BEFORE the quarter when the class is taken. Prerequisites are defined as a list of class IDs in the data you will be given. Please remember that the seasons go in this order: Fall, Winter, Spring, Summer, and they repeat each year.\n\nThe student must complete the following classes to graduate: $courseData"
                ],
                [
                    "role" => "user",
                    "content" => "I have already taken the classes with these IDs: $priorCoursesData.\n\nI only want to take $perQuarter classes each quarter.\n\nThis is my current schedule: $scheduleData.\n\nPlease update the schedule using these notes: $notes"
                ]
            ],
            "response_format" => [ "type" => "json_object" ],
            "frequency_penalty" => 0.5
        ];

        return $this->request($requestBody);
    }
}