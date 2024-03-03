const API_URL = `${window.origin}/485/advising-tool/api`;

let courses = [];
let courseRows = [];

const modal = {
    root:               document.getElementById("course-modal"),
    background:         document.getElementById("modal-background"),
    heading:            document.querySelector("#course-modal h2"),
    name:               document.getElementById("course-name"),
    priority:           document.getElementById("course-priority"),
    prerequisitesRoot:  document.getElementById("course-prerequisites"),
    addPrerequisiteBtn: document.getElementById("course-modal-add-prerequisite"),
    closeBtn:           document.getElementById("course-modal-close"),
    submitBtn:          document.getElementById("course-modal-submit"),

    // ID of the course being edited
    editingId:          null,

    // True if the user is creating a new course, false if the user is editing an existing course
    creating:           false
};

const coursesTableBody = document.querySelector("table#courses tbody");

const PLACEMENT_NAMES = {
    [-2]: "Beginning",
    [-1]: "Early",
    [0]:  "Middle",
    [1]:  "Late",
    [2]:  "End"
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

    const removeBtn = row.appendChild(document.createElement("button"));
    removeBtn.textContent = "Remove";

    removeBtn.onclick = () => row.remove();
}

function openCourseModal() {
    // Make the modal visible to the user
    modal.root.className = modal.background.className = "visible";

    // Clear prior modal fields
    modal.heading.textContent = `Loading...`;
    modal.name.value = "";
    modal.prerequisitesRoot.innerHTML = "";
    modal.priority.value = 0;

    modal.creating = false;
}

function beginAddCourse() {
    openCourseModal();
    modal.heading.textContent = "Add new course";
    modal.creating = true;
}

async function editCourse(course) {
    openCourseModal();

    // Request full data from the server
    const response = await fetch(`${API_URL}/courses/${course}`);
    const data = await response.json();

    // Initialize modal fields
    modal.name.value = data['Name'];
    modal.priority.value = data['Priority'];

    data['Prerequisites'].forEach(prerequisite => createPrerequisiteRow(prerequisite['ID']));

    modal.heading.textContent = `Editing ${data['Name']}`;

    modal.editingId = course;
}

async function deleteCourse(course) {
    if (!confirm(`Delete ${course['Name']}?`)) return;

    const id = course['ID'];

    const response = await fetch(`${API_URL}/courses/${id}`, {
        method: "DELETE"
    });

    courseRows[id].root.remove();
    delete courseRows[id];
    delete courses[id];
}

async function submitCourse() {
    const body = {
        "ID": modal.editingId,
        "Name": modal.name.value,
        "Prerequisites": [],
        "Priority":  modal.priority.value
    };

    for (const row of modal.prerequisitesRoot.querySelectorAll(".prerequisite-row")) {
        const course = row.querySelector(".prerequisite-course");

        const courseId = course.value;
        if (isNaN(courseId)) continue;

        body["Prerequisites"].push({
            "ID": courseId
        });
    }

    const uri = modal.creating
        ? `${API_URL}/courses`
        : `${API_URL}/courses/${modal.editingId}`;

    const response = await fetch(uri, {
        method: modal.creating ? "POST" : "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(body)
    });

    const result = await response.json();

    if (modal.creating) insertCourseRow(result);
    else updateCourseRow(result);

    alert(response.ok
        ? `Successfully submitted "${body["Name"]}"`
        : `Unable to submit course data: ${response.status}`);

    hideModal();
}

function hideModal() {
    modal.root.className = modal.background.className = "";
    modal.editingId = null;
}

function updateCourseRow(course) {
    const row = courseRows[course['ID']];
    if (row == null) throw new Error(`There is no course with ID ${course['ID']}`);

    row.id.textContent = course['ID'];
    row.name.textContent = course['Name'];
    row.priority.textContent = PLACEMENT_NAMES[course['Priority']] ?? course['Priority'];
    row.prerequisites.textContent = course['NumPrerequisites'];
}

function createCourseRow() {
    let row = {};

    row.root = document.createElement("tr");
    row.id = row.root.appendChild(document.createElement("td"));
    row.name = row.root.appendChild(document.createElement("td"));
    row.priority = row.root.appendChild(document.createElement("td"));
    row.prerequisites = row.root.appendChild(document.createElement("td"));
    row.edit = row.root.appendChild(document.createElement("td"));
    row.editBtn = row.edit.appendChild(document.createElement("button"));
    row.delete = row.root.appendChild(document.createElement("td"));
    row.deleteBtn = row.delete.appendChild(document.createElement("button"));

    row.editBtn.textContent = "Edit";
    row.deleteBtn.textContent = "Delete";

    return row;
}
function insertCourseRow(course) {
    const row = createCourseRow();

    courseRows[course['ID']] = row;
    coursesTableBody.appendChild(row.root);
    row.editBtn.onclick = () => editCourse(course['ID']);
    row.deleteBtn.onclick = () => deleteCourse(course);
    updateCourseRow(course);
}

async function initializeAdminPage() {
    const url = `${API_URL}/courses`;
    const init = {
        method: "GET"
    };
    const response = await fetch(url, init);
    const data = await response.json();

    data.forEach(course => {
        courses[course['ID']] = course;
    })

    courses.forEach(course => insertCourseRow(course));

    modal.addPrerequisiteBtn.onclick = createPrerequisiteRow;
    modal.submitBtn.onclick = submitCourse;
    modal.closeBtn.onclick = modal.background.onclick = hideModal;

    document.getElementById("add-course-btn").onclick = beginAddCourse;
}

initializeAdminPage();