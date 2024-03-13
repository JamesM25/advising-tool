# API

The Advising Tool exposes a RESTful API to allow the user to access or modify course data used by the application.

At the time of writing, these routes are only used on the admin page.

## GET `/api/courses`
Returns an array containing all courses.

Courses are provided as objects using the following format:

| Field            | Type   | Notes                                                                                                                                                                  |
|------------------|--------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| ID               | int    | Unique, numeric identifier for the course.                                                                                                                             |
| Name             | string | User-facing course name, e.g. "SDEV101."                                                                                                                               |
| Priority         | int    | Course priority. Negative values indicate the course should be placed earlier into the schedule, positive values indicate it should be placed further towards the end. |
| NumPrerequisites | int    | Number of prerequisite courses that must be taken before this course.                                                                                                  |

## GET `/api/course/{ID}`
Returns data for the course with the ID given at the end of the URL, including a list of prerequisites.

The course data is given in the following format:

| Field            | Type   | Notes                                                                                                                                                                  |
|------------------|--------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| ID               | int    | Unique, numeric identifier for the course.                                                                                                                             |
| Name             | string | User-facing course name, e.g. "SDEV101."                                                                                                                               |
| Priority         | int    | Course priority. Negative values indicate the course should be placed earlier into the schedule, positive values indicate it should be placed further towards the end. |
| NumPrerequisites | int    | Number of prerequisite courses that must be taken before this course.                                                                                                  |
| Prerequisites    | array  | Array of courses that must be completed before the student is eligible for this course.                                                                                |

Elements in the `Prerequisites` array are given in the following format:

| Field | Type | Notes                         |
|-------|------|-------------------------------|
| ID    | int  | ID of the prerequisite course |

Successful requests will return HTTP status 200. If the ID in the URL does not correspond with any course data, the status will be 404.

## POST `/api/course`
Inserts a new course object into the database.

### Input
The input should be a JSON course object using the following format:

| Field            | Type   | Notes                                                                                                                                                                  |
|------------------|--------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Name             | string | User-facing course name, e.g. "SDEV101."                                                                                                                               |
| Priority         | int    | Course priority. Negative values indicate the course should be placed earlier into the schedule, positive values indicate it should be placed further towards the end. |
| Prerequisites    | array  | Array of courses that must be completed before the student is eligible for this course.                                                                                |

Note that this is largely equivalent to the format used by the `GET /api/courses/{ID}` route,
except for the `ID` and `NumPrerequisites` fields, which are excluded as they are redundant when creating new data.

If redundant fields are included in the input, they are ignored.

### Output
Upon completion, this route will return an updated course object in the same format as used by `GET /api/courses/{ID}`.
The `Name`, `Priority` and `Prerequisites` fields are the same as the input.

## PUT `/api/course/{ID}`
Updates an existing course object in the database.

Input and output data are provided in the same formats as `POST /api/courses`.

## DELETE `/api/course/{ID}`
Deletes a course object with the given ID from the database.