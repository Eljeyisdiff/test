document.addEventListener("DOMContentLoaded", function() {

const modalConfirmDialog = document.querySelector("#dialog-confirm-queue");
const modalAlreadyDialog = document.querySelector("#dialog-already-queued");
const modalCloseDialog = document.querySelectorAll("dialog .close-button");
const modalGotoDialog = document.querySelector("#dialog-already-queued .confirm-button.goto-queue");

const joinButtons = document.body.querySelectorAll("main .office-grids button");
const filterBar = document.querySelector("main .search-bar input");

const dialogSubmitQueue = document.querySelector("dialog #select-reason");

let officeReasons;

let dialogOfficeID;

//for refresh
let isModalOpen = false;

document.onload = fetchStaticData();

function filterOffice(rawInput) {
  var upperCaseOutput = rawInput.toUpperCase();
  var office_entries = document.querySelectorAll("main .office-grids .office-entry");

  for (let i = 0, o; o = office_entries[i]; i++) {
    if (o.innerHTML.toUpperCase().indexOf(upperCaseOutput) > -1) {
      o.style.display = "flex";
    } else {
      o.style.display = "none";
    }
  }
}

function dialogConfirmQueue(event) {

    const titleParentId = event.target.parentElement.id;
    dialogOfficeID = titleParentId.replace(/[^0-9]/g, '');

    // example output: document.querySelector(#office-id-[number] #title).textContent();
    const titleContent = document.querySelector(`#${titleParentId}` + " " +".title").textContent;

    document.querySelector("#dialog-confirm-queue #dialog-title").textContent = titleContent;
    

    const createDivMain = document.createElement("div");
    createDivMain.setAttribute("class", "selection removable");
    document.querySelector(".select-reason").prepend(createDivMain);

    // HTML elements are appended in reverse

    dialogCreateOtherRadio();
    // event.target.value example output: 1
    // (1 in this case may correspond to "Registrar's Office", see values of button on html file)
    for (let content of officeReasons[event.target.value]) {
      dialogCreateCheckbox(content);
    }

    // Input event for radio buttons
    const dialogRadiButtons = document.querySelectorAll("dialog #select-reason input.radio-button");

    dialogRadiButtons.forEach(button => {
      button.addEventListener("change", (event) => {
        const inputField = document.querySelector("dialog .reason-other-input");
        if(event.target.value == "other") {
          inputField.removeAttribute('disabled');
        } else {
          inputField.setAttribute('disabled', 'disabled');
        }
      })
    })

    isModalOpen = true;

    modalConfirmDialog.showModal();
}

// Add checkboxes dynamically based on the office
function dialogCreateCheckbox(content) {
    const createDiv = document.createElement("div");
    const createInput = document.createElement("input");
    const createLabel = document.createElement("label");

    createInput.setAttribute("class", "radio-button");
    createInput.setAttribute("type", "radio");
    createInput.setAttribute("name", "purpose");
    createInput.setAttribute("value", content);

    createLabel.textContent = content;

    createDiv.append(createInput);
    createDiv.append(createLabel);
    document.querySelector(".removable").prepend(createDiv);
}

function dialogCreateOtherRadio() {
  const createDiv = document.createElement("div");
  const createInput = document.createElement("input");
  const createLabel = document.createElement("label");
  const createOtherInput = document.createElement("input");

  createInput.setAttribute("class", "radio-button");
  createInput.setAttribute("type", "radio");
  createInput.setAttribute("name", "purpose");
  createInput.setAttribute("value", "other");
  createInput.setAttribute("id", "otherPurposeRadio");

  createLabel.textContent = "Other (Please Specify)";
  createLabel.setAttribute("for", "otherPurposeRadio");

  createOtherInput.setAttribute("class", "reason-other-input");
  createOtherInput.setAttribute("type", "text");
  createOtherInput.setAttribute("id", "otherPurposeInput");
  createOtherInput.setAttribute("name", "other_purpose");
  createOtherInput.setAttribute("disabled", "disabled"); // Initially disabled

  // Enable the input when the radio button is selected
  createInput.addEventListener('change', function() {
      createOtherInput.disabled = !this.checked;
      if (!this.checked) {
          createOtherInput.value = ''; // Clear the input if not checked
      }
  });

  createDiv.append(createInput);
  createDiv.append(createLabel);
  createDiv.append(createOtherInput); // Append the text input

  document.querySelector(".removable").prepend(createDiv);
}

function dialogAlreadyQueued() {
  isModalOpen = true;
  modalAlreadyDialog.showModal();
}

/* Event Listeners */

// remove dynamic checkboxes and reset values on close
modalConfirmDialog.addEventListener("close", function() {
    const element = document.querySelector("#dialog-confirm-queue .removable");
    document.querySelector("#dialog-confirm-queue .reason-other-input").value = "";
    dialogOfficeID = null;
    element.remove();
})

filterBar.addEventListener("keyup", function() {
  filterOffice(filterBar.value);
})

//get office id from session
let currentOfficeID;

async function fetchCurrentOfficeID() {
    try {
        const response = await fetch("../api/get_office_id.php");
        const data = await response.json();
        
        currentOfficeID = data.office_id; // Store the office ID for later use
        console.log("Current Office ID:", currentOfficeID);
    } catch (error) {
        console.error("Error fetching office ID:", error);
    }
}

// Add event listener for join buttons
joinButtons.forEach(button => {
  button.addEventListener("click", async (event) => {
    const officeId = event.target.value;
    const userId = "<?php echo $_SESSION['user_id']; ?>";

    if (officeId === currentOfficeID) {
      // Redirect to the queue view if the user is already in the queue for this office
      window.location.href = 'queueview_user.php';
      return; // Exit the function to prevent further execution
    }

    // Send request to check if user is in queue
    const formData = new FormData();
    formData.append("user_id", userId);
    formData.append("office_id", officeId);

    try {
      const response = await fetch("../api/check_queue.php", {
        method: "POST",
        body: formData,
      });
      
      const result = await response.json();

      if (result.inQueue) {
        // Show the "Already Queued" modal
        dialogAlreadyQueued();
      } else {
        // Show the "Join Queue" modal
        dialogConfirmQueue(event);
      }
      
    } catch (error) {
      console.error("Error checking queue status:", error);
    }
  });
});

modalCloseDialog.forEach(button => {
  button.addEventListener("click", (event) => {
    isModalOpen = false;
    modalConfirmDialog.close();
    modalAlreadyDialog.close();
  })
})

modalGotoDialog.addEventListener("click", function() {
  window.location = "http://localhost";
})




// const sortButtonMenu = document.body.querySelector("main .top-bar .search-bar .dropdown.sort-by #sort-dropdown")
// sortButtonMenu.addEventListener("click", function(event) {
//   const sortText = document.querySelector("main .top-bar .search-bar .dropdown.sort-by .btn.btn-secondary p.sort-type");
//   sortText.textContent = event.target.value;
// })

async function fetchStaticData() {
  const url = "../api/purposes_api.php";
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }

    officeReasons = await response.json();

  } catch (error) {
    console.error(error.message);
  }
}

async function sendModalData(dataToSend) {
  try {
    const response = await fetch("../api/join_queue_api.php", {
      method: "POST",
      body: dataToSend,
    });
  } catch (e) {
    console.error(e);
  }
}

dialogSubmitQueue.addEventListener("submit", async function (event) {
  event.preventDefault();
  console.log("Confirm and join queue button clicked");
  const formData = new FormData(dialogSubmitQueue);

  // Get the selected purpose
  const selectedPurpose = formData.get('purpose');
  const otherReason = document.getElementById('otherPurposeInput').value; // Fetch the other reason from the textbox

  // Prepare the data to send
  const dataToSend = {
    'user_id': formData.get('user_id'),
    'office_id': dialogOfficeID,
    'service_details': selectedPurpose === 'other' ? otherReason : selectedPurpose, // Use otherReason if 'other' selected
  };

  

  try {
    const response = await fetch('../api/join_queue_api.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(dataToSend),
    });
    
    const result = await response.json();

    if (result.success) {
      // Redirect to the queue view page after successful join
      
      window.location.href = `queueview_user.php?ticket_id=${result.ticket_id}`;
    } else {
      alert(result.message || 'Failed to join the queue.'); // Improved error feedback
    }
  } catch (error) {
    console.error('Error joining queue:', error);
    alert('An error occurred while trying to join the queue. Please try again later.'); // User-friendly message
  }
});

// Call the function to fetch the office ID when the script loads
fetchCurrentOfficeID();

// for the traffic
// document.querySelectorAll('.dropdown-item').forEach(item => {
//   item.addEventListener('click', function() {
//       const sortType = this.value;  // Get the selected sort type
//       document.querySelector('.sort-type').innerText = sortType;  // Update the displayed sort type
      
//       // Send AJAX request to get filtered offices
//       fetchOffices(sortType.toLowerCase().replace(' ', '-'));  // Send low-traffic or high-traffic as the parameter
//   });
// });

// function fetchOffices(sortType) {
//   fetch('../api/get_offices.php?sort=' + sortType)
//   .then(response => response.text())
//   .then(data => {
//       document.getElementById('office-grids').innerHTML = data;
//   })
//   .catch(error => console.error('Error fetching offices:', error));
// }

//for search
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-office");
  const officeEntries = document.querySelectorAll(".office-entry");

  // Event listener for the search input
  searchInput.addEventListener("input", function() {
    const searchTerm = searchInput.value.toLowerCase();

    // Loop through all office entries
    officeEntries.forEach((office) => {
      const officeName = office.querySelector(".title").textContent.toLowerCase();

      // Show or hide office based on whether it matches the search term
      if (officeName.includes(searchTerm)) {
        office.style.display = "flex"; // Show office
      } else {
        office.style.display = "none"; // Hide office
      }
    });
  });
});

// Hide the message after 3 seconds
document.addEventListener("DOMContentLoaded", function() {
  setTimeout(function() {
      var messageDiv = document.getElementById("queue-message");
      if (messageDiv) {
          messageDiv.style.display = "none";
      }
  }, 3000); // 3000 milliseconds = 3 seconds
});

setTimeout(function() {
  const queueMessage = document.getElementById("queue-message");
  const warningMessage = document.getElementById("warning-message");

  if (queueMessage) queueMessage.style.display = "none";
  if (warningMessage) warningMessage.style.display = "none";
}, 3000);

//refresh the page
// Refresh the page every 5 seconds if no modal is open
setInterval(function() {
  if (!isModalOpen) {
    location.reload();
  }
}, 5000);

});
