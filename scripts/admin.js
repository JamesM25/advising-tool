const API_URL = `${window.origin}/485/advising-tool/api`;

let courses = [];

const modal = {
    root:               document.getElementById("course-modal"),
    background:         document.getElementById("modal-background"),
    heading:            document.querySelector("#course-modal h2"),
    name:               document.getElementById("course-name"),
    prerequisitesRoot:  document.getElementById("course-prerequisites"),
    addPrerequisiteBtn: document.getElementById("course-modal-add-prerequisite"),
    closeBtn:           document.getElementById("course-modal-close"),
    submitBtn:          document.getElementById("course-modal-submit"),

    // ID of the course being edited
    editingId:            null
};

function createPrerequisiteRow(courseId) {
    const row = modal.prerequisitesRoot.appendChild(document.createElement("div"));
    row.className = "prerequisite-row";

    const select = row.appendChild(document.createElement("select"));
    select.className = "prerequisite-course";

    let options = "<option>(None)</option>";

    courses.forEach(course => {
        const id = course['ID'];
        const name = course['Name'];
        const selected = id === courseId;

        options += selected
            ? `<option value="${id}" selected="selected">${name}</option>`
            : `<option value="${id}">${name}</option>`;
    });

    select.innerHTML = options;
}

async function editCourse(course) {
    // Make the modal visible to the user
    modal.root.className = modal.background.className = "visible";

    // Clear prior modal fields
    modal.heading.textContent = `Loading...`;
    modal.name.value = "";
    modal.prerequisitesRoot.innerHTML = "";

    // Request full data from the server
    const response = await fetch(`${API_URL}/courses/${course}`);
    const data = await response.json();

    // Initialize modal fields
    modal.name.value = data['Name'];

    data['Prerequisites'].forEach(prerequisite => createPrerequisiteRow(prerequisite['ID']));

    modal.heading.textContent = `${data['Name']}`;

    modal.editingId = course;
}

async function submitCourse() {
    const body = {
        "ID": modal.editingId,
        "Name": modal.name.value,
        "Prerequisites": []
    };

    for (const row of modal.prerequisitesRoot.querySelectorAll(".prerequisite-row")) {
        const course = row.querySelector(".prerequisite-course");

        const courseId = course.value;
        if (isNaN(courseId)) continue;

        body["Prerequisites"].push({
            "ID": courseId
        });
    }

    const response = await fetch(`${API_URL}/courses/${modal.editingId}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(body)
    });

    alert(response.ok
        ? `Successfully updated "${body["Name"]}"`
        : `Unable to update course data: ${response.status}`);

    hideModal();
}

function hideModal() {
    modal.root.className = modal.background.className = "";
    modal.editingId = null;
}

async function initializeAdminPage() {
    const coursesTableBody = document.querySelector("table#courses tbody");

    /*
     * API ROUTES:
     *
     * GET /api/courses
     *  Returns an array of all courses.
     *  Example: GET /api/courses
     *  [
     *      {
     *          GroupNum: null,
     *          ID: 1,
     *          Name: "MATH97"
     *      },
     *      {
     *          GroupNum: null,
     *          ID: 2,
     *          Name: "ENG101"
     *      },
     *      ...
     *  ]
     *
     * GET /api/prerequisites/[Course ID]
     *  Returns an array of all prerequisites corresponding with the given course ID.
     *  Example: GET /api/prerequisites/22
     *  [
     *      {
     *          ClassID: 7,
     *          GroupNum: null,
     *          PrerequisiteID: 1
     *      }
     *  ]
     *
     */


    const url = `${API_URL}/courses`;
    const init = {
        method: "GET"
    };
    const response = await fetch(url, init);
    const data = await response.json();

    data.forEach(course => {
        courses[course['ID']] = course;
    })

    courses.forEach(course => {
        coursesTableBody.innerHTML += `
        <tr>
            <td>${course['ID']}</td>
            <td>${course['Name']}</td>
            <td>${course['NumPrerequisites']}</td>
            <td>${course['GroupNum'] ?? ""}</td>
            <td>
                <button onclick="editCourse('${course['ID']}')">Edit</button>
            </td>
        </tr>`;

    });

    modal.addPrerequisiteBtn.onclick = createPrerequisiteRow;
    modal.submitBtn.onclick = submitCourse;
    modal.closeBtn.onclick = modal.background.onclick = hideModal;
}

initializeAdminPage();