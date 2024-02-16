const prerequisiteModal = document.getElementById("prerequisite-modal");
const modelBackground = document.getElementById("modal-background");
const prerequisiteModalHeading = document.querySelector("#prerequisite-modal h2");

function editPrequisites(course) {
    prerequisiteModalHeading.textContent = `Prerequisites for ${course}`;
    prerequisiteModal.className = modelBackground.className = "visible";
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

    // TODO: Account for different hostnames
    const url = 'https://jmotherwell.greenriverdev.com/485/advising-tool/api/courses';
    const init = {
        method: "GET"
    };
    const response = await fetch(url, init);
    const data = await response.json();

    for (const course of data) {
        coursesTableBody.innerHTML += `
        <tr>
            <td>${course['ID']}</td>
            <td><input type="text" required value="${course['Name']}"></td>
            <td><input type="number" value="${course['GroupNum']}"></td>
            <td>
                <button onclick="editPrequisites('${course['Name']}')">Edit</button>
            </td>
            <td>
                <button>Update</button>
            </td>
        </tr>`;
    }

    const prerequisiteFieldsContainer = document.getElementById("prerequisite-modal-fields");
    for (const course of data) {
        const elementId = `checkbox-${course['ID']}`;
        prerequisiteFieldsContainer.innerHTML += `
        <div>
            <input type="checkbox" id="${elementId}">
            <label for="${elementId}">${course['Name']}</label>
        </div>`;
    }

    document.getElementById("prerequisite-modal-close").onclick = () => {
        prerequisiteModal.className = modelBackground.className = "";
    }
}

initializeAdminPage();