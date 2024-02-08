const prerequisiteModal = document.getElementById("prerequisite-modal");
const modelBackground = document.getElementById("modal-background");
const prerequisiteModalHeading = document.querySelector("#prerequisite-modal h2");

function editPrequisites(course) {
    prerequisiteModalHeading.textContent = `Prerequisites for ${course}`;
    prerequisiteModal.className = modelBackground.className = "visible";
}

async function initializeAdminPage() {
    const coursesTableBody = document.querySelector("table#courses tbody");

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
            <td>${course['id']}</td>
            <td><input type="text" required value="${course['name']}"></td>
            <td><input type="number" value="${course['group']}"></td>
            <td>
                <button onclick="editPrequisites('${course['name']}')">Edit</button>
            </td>
            <td>
                <button>Update</button>
            </td>
        </tr>`;
    }

    const prerequisiteFieldsContainer = document.getElementById("prerequisite-modal-fields");
    for (const course of data) {
        const elementId = `checkbox-${course['id']}`;
        prerequisiteFieldsContainer.innerHTML += `
        <div>
            <input type="checkbox" id="${elementId}">
            <label for="${elementId}">${course['name']}</label>
        </div>`;
    }

    document.getElementById("prerequisite-modal-close").onclick = () => {
        prerequisiteModal.className = modelBackground.className = "";
    }
}

initializeAdminPage();