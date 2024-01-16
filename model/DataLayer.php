<?php
class DataLayer {
    const prerequisites = [
        "cs108|cs109" => [ "math97" ],
        "math141|math147" => [ "math97" ],
        "sdev121" => [ "cs108|cs109"],
        "sdev117" => [ "sdev106" ],
        "sdev218" => [ "math97" ],
        "sdev219" => [ "sdev218" ],
    ];

    public static function getRequiredCourse() {
        return array(
            "math97",
            "math141|math147",
            "math146|math256",
            "eng101",
            "eng126|eng127|eng128|eng235",
            "cmst210|cmst220|cmst230|cmst238",
            "cs108|cs109",
            "sdev106",
            "sdev117",
            "sdev218",
            "sdev219",
            "sdev220",
            "sdev201",
            "sdev280"
        );
    }
}