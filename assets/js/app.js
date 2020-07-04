// DOM selectors
const notesListItem = document.querySelectorAll(".list-group-item.notes");
const notesListAdd = document.querySelector(".list-group-item-action");

const noteForm = document.querySelector("#note-form");
const titleInput = document.querySelector("#title");
const noteInput = document.querySelector("#note");
const submitBtn = document.querySelector("#submit");
const fetchUrl = "fetch-note.php";
const deleteUrl = "delete-note.php";

// Notes List event listeners
notesListItem.forEach(item => {
  // Sets active class on hover
  item.addEventListener("mouseenter", () => {
    item.classList.add("active");
  });
  item.addEventListener("mouseleave", () => {
    item.classList.remove("active");
  });

  // On click, fetches the associated note and modifies form
  item.addEventListener("click", fetchNote);

  // Delete button listener
  item.childNodes[1].addEventListener("click", deleteNote);
});

notesListAdd.addEventListener("click", () => {
  // Remove old disabled class
  if (document.querySelector(".disabled")) {
    document.querySelector(".disabled").classList.remove("disabled");
  }

  // Add button
  submitBtn.innerText = "Add Note";
  submitBtn.className = "btn btn-block btn-primary";
  submitBtn.name = "create";

  // Clear inputs
  titleInput.value = "";
  noteInput.innerText = "";

  // Form action handling
  noteForm.action = "add-note.php";
});

// Callbacks
function fetchNote(e) {
  // Set title
  const title = e.target.childNodes[0].nodeValue.trim();
  titleInput.value = title;

  // Fetch and set note
  let request = new XMLHttpRequest();
  request.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      const result = JSON.parse(this.responseText);
      // Set id
      const id = result.id;
      addHiddenInput(id);

      // Set note
      const note = result.note;
      noteInput.innerText = note;
    }
  };
  request.open("GET", fetchUrl + "?title=" + title, true);
  request.responseType = "text";
  request.send();

  // Remove old disabled class
  if (document.querySelector(".disabled")) {
    document.querySelector(".disabled").classList.remove("disabled");
  }

  // Set disabled class
  e.target.classList.add("disabled");

  // Edit button
  submitBtn.innerText = "Edit Note";
  submitBtn.className = "btn btn-block btn-info";
  submitBtn.name = "edit";

  // Form action handling
  noteForm.action = "edit-note.php";
}

function deleteNote(e) {
  const noteItem = e.target.parentElement.parentElement;
  const noteId = e.target.parentElement.parentElement.id;

  noteItem.removeEventListener("click", fetchNote);

  let request = new XMLHttpRequest();
  request.open("POST", deleteUrl, true);
  request.setRequestHeader("Content-Type", "application/json");
  request.send(JSON.stringify(noteId));

  noteItem.remove();

  // Add button
  submitBtn.innerText = "Add Note";
  submitBtn.className = "btn btn-block btn-primary";
  submitBtn.name = "create";

  // Clear inputs
  titleInput.value = "";
  noteInput.innerText = "";

  // Form action handling
  noteForm.action = "add-note.php";
}

function addHiddenInput(id) {
  // Remove old hidden input
  if (document.querySelector("#note_id")) {
    noteForm.removeChild(document.querySelector("#note_id"));
  }

  // Add new hidden input
  const input = document.createElement("input");
  input.type = "hidden";
  input.name = "note_id";
  input.value = id;
  input.id = "note_id";
  noteForm.appendChild(input);
}
