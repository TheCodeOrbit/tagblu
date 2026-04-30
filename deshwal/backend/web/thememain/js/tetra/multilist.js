// alert(Recordlist);
if(Recordlist !='')
    {
        $("#searchInput").removeClass(typeofdata);
        
    }

    function fetchFilteredList() {
        const dropdownList = document.getElementById(fieldname);
        const searchInput = document.getElementById("searchInput");
    
        // Ensure dropdownList is valid
        if (!dropdownList) {
            console.error("Dropdown list element not found.");
            return;
        }
    
        // Fetch and check dropdown value
        const dropdownValue = dropdownList.value;
        // alert(dropdownValue)
        if (dropdownValue !== '') {
            // Remove typeofdataclass
            $("#searchInput").removeClass(typeofdata);
        }
    
        const input = searchInput.value.trim(); // Get the input value
    
        // Clear the dropdown list if there is no input
        if (!input) {
            console.log(input);
            $(".availableList").html(""); // Clear the previous list
            return; // Exit early as no input means no data to fetch
        }
    
        // If input is provided, fetch new data
        fetch(`searchusers?query=${encodeURIComponent(input)}`)
            .then((response) => response.json())
            .then((data) => {
                // Clear previous items (in case any were added previously)
                dropdownList.innerHTML = "";
    
                // Render the new available items
                renderAvailableItems(data);
            })
            .catch((error) => console.error("Error fetching data:", error));
    }
    
    // Function to render items into the dropdown list
    function renderAvailableItems(data) {
        const dropdownList = document.getElementById(fieldname);
    
        // Add each item to the dropdown list
        data.forEach((item) => {
            const li = document.createElement("li");
            li.textContent = item.showfield;
            li.dataset.id = item.id; // Save the ID in a dataset attribute
            li.onclick = () => addToContainer(item.id, item.showfield);
            dropdownList.appendChild(li);
        });
    }
    

// Function to render available items in the list
function renderAvailableItems(items) {
	const availableList = document.getElementById('availableList');
    availableList.innerHTML = ''; // Clear the list before rendering

    items.forEach(item => {
    	console.log(item.showfield);
        const li = document.createElement('li');
        li.textContent = item.showfield;
        li.setAttribute('data-id', item.id);
        li.setAttribute('data-name', item.showfield);
        li.classList.add('item');
        li.addEventListener('click', function() {
        	addItemToAddedList(item.id,item.showfield);
            li.style.display = 'none'; // Hide the item after adding it
        });
        availableList.appendChild(li);
    });
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    let searchQuery = this.value.toLowerCase();
    let items = document.querySelectorAll('#availableList .item');
    
    items.forEach(item => {
        if (item.textContent.toLowerCase().includes(searchQuery)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Function to add item to the added list
function addItemToAddedList(id,name) {
    const container = document.getElementById("added-items");

  // Check if the item is already added
  if (
    Array.from(container.children).some(
      (child) => child.dataset.id === String(id)
    )
  ) {
    alert("User already added!");
    return;
  }

  // Create a new div for the selected item
  const newItem = document.createElement("span");
  newItem.textContent = name;
  newItem.dataset.id = id; // Store the ID
  // Add a class to the newItem
  newItem.classList.add("attendee");

  // Optionally add a remove button for each item
  const removeBtn = document.createElement("span");
  removeBtn.textContent = "X";
  removeBtn.style.marginLeft = "10px";
  removeBtn.style.border = "none";
  removeBtn.style.background = "none";
  removeBtn.style.cursor = "pointer";
  removeBtn.onclick = () => newItem.remove();

  newItem.appendChild(removeBtn);
  container.appendChild(newItem);
  updateSelectedItems();
}

// Initially fetch and render available items
// fetchAvailableItems();
function updateSelectedItems() {
    const selectedItems = [];
    document.querySelectorAll('.attendee').forEach(item => {
    	// alert(item);
    	// alert(item.dataset.id);
        selectedItems.push(item.dataset.id); // Collect item IDs
    });
    document.getElementById(fieldname).value = selectedItems.join(',');
    checkval = document.getElementById(fieldname).value;
    // alert(checkval);
    if(checkval =='')
    $("#searchInput").addClass(typeofdata);
    else
    $("#searchInput").removeClass(typeofdata);
}
$('.remitem').on('click', function() {
    removethis.call(this);  // Use .call(this) to properly set 'this' inside removethis
});
function removethis(){
    var attendeeSpan = $(this).closest('span.attendee');
    
    if (attendeeSpan.length > 0) {
        // alert("Span found! Removing...");
        attendeeSpan.remove();  // Remove the entire span.attendee that contains the clicked .remitem
    } else {
        // alert("No span found.");
    }
    
    updateSelectedItems();  // Update selected items if necessary
}

