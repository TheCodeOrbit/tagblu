document.addEventListener("DOMContentLoaded", function () {
  const stages = document.querySelectorAll(".stage");

  const stageNameElement = document.querySelector(".stage-info-name"); // Stage Name

  const stageDescriptionElement = document.querySelector("#stage-description"); // Description
  const datetimeElement = document.querySelector(".stage-info-datetime");
  const durationElement = document.querySelector(".stage-info-duration");
  const markAsCurrentBtn = document.querySelector(".mark-as-current"); // "Mark as Current" button
  let currentStage = document.querySelector(".stage.active");

  const leadId = document.querySelector("#leadid").value; // Get lead ID
  const userId = document.querySelector("#user_id").value; // Get user ID (if exists)

  // Function to reset styles and remove active/visited state
  function resetStyles() {
    stages.forEach((stage) => {
      stage.style.textDecoration = "none";
      stage.classList.remove("visited");
      stage.classList.remove("active");
    });
  }

  // Function to handle stage highlighting
  function highlightStages(visitedStages, currentStageId) {
    stages.forEach((stage) => {
      const stageId = stage.getAttribute("data-stage_id");

      // Mark visited stages
      if (visitedStages.includes(stageId)) {
        stage.classList.add("visited");
      }

      // Mark the current stage
      if (stageId === currentStageId) {
        stage.classList.add("active");
        stage.style.textDecoration = "underline";

        // Update stage details
        stageNameElement.textContent = stage.getAttribute("data-stage");
        stageDescriptionElement.textContent =
          stage.getAttribute("data-description");

        markAsCurrentBtn.style.display = "block";
      }
    });
  }

  // AJAX request to fetch initial stage data on page load
  $.ajax({
    url: "update-lead-datetime", // Replace with your endpoint
    type: "POST",
    data: {
      lead_id: leadId,
      user_id: userId,
      _csrf: yii.getCsrfToken(),
    },
    success: function (response) {
      if (response.status === "success") {
        const visitedLinks = response.visited_link || [];
        const currentStageId = response.current_stage_id || null;

        // Highlight stages
        highlightStages(
          visitedLinks.map((link) => link.prevalue), // Extract visited stage IDs
          currentStageId
        );

        // Update datetime and duration for the active stage
        if (response.changedon) {
          datetimeElement.textContent = response.changedon;
        }
        if (response.duration) {
          durationElement.textContent = response.duration;
        }
      } else {
        console.error("Failed to fetch stage data:", response.message);
      }
    },
    error: function () {
      console.error("Error loading stage data from the server.");
    },
  });

  // Event listener for stage clicks
  stages.forEach((stage) => {
    stage.addEventListener("click", function () {
      resetStyles(); // Reset styles
      this.style.textDecoration = "underline"; // Underline the clicked stage
      this.classList.add("active"); // Mark it as active

      // Update the stage details
      stageNameElement.textContent = this.getAttribute("data-stage");
      stageDescriptionElement.textContent =
        this.getAttribute("data-description");

      // Show "Mark as Current" button
      markAsCurrentBtn.style.display = "block";

      // Optionally send an AJAX request when a stage is clicked
    });
  });

  // Initialize: Set default Stage Name and Description
  if (currentStage) {
    stageNameElement.textContent = currentStage.getAttribute("data-stage");
    stageDescriptionElement.textContent =
      currentStage.getAttribute("data-description");
  }

  markAsCurrentBtn.style.display = "none"; // Initially hide the button

  // Function to reset styles for all stages
  function resetStyles() {
    stages.forEach((stage) => {
      stage.style.textDecoration = "none"; // Remove underline
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    const stages = document.querySelectorAll(".stage");
    const stageNameElement = document.querySelector(".stage-info-name");
    const stageDescriptionElement =
      document.querySelector("#stage-description");
    const markAsCurrentBtn = document.querySelector(".mark-as-current");

    // Function to reset styles for all stages
    function resetStyles() {
      stages.forEach((stage) => {
        stage.style.textDecoration = "none";
        stage.classList.remove("visited"); // Clear visited class
      });
    }

    // Fetch lead ID and user ID from the hidden input fields
    const leadId = document.querySelector("#leadid").value;
    const userId = document.querySelector("#user_id").value;

    // AJAX request to fetch stage data when the page loads
    $.ajax({
      url: "update-lead-datetime", // Replace with your endpoint
      type: "POST",
      data: {
        lead_id: leadId,
        user_id: userId,
        _csrf: yii.getCsrfToken(),
      },
      success: function (response) {
        if (response.status === "success") {
          // Update the currently active stage details
          if (response.changedon) {
            document.querySelector(".stage-info-datetime").textContent =
              response.changedon;
          }
          if (response.duration) {
            document.querySelector(".stage-info-duration").textContent =
              response.duration;
          }

          // Highlight the visited stages
          if (response.visited_link && Array.isArray(response.visited_link)) {
            stages.forEach((stage) => {
              const stageId = stage.getAttribute("data-stage_id");

              // Check if the stageId exists as a prevalue in visited_link
              const isMatched = response.visited_link.some(
                (link) => link.prevalue === stageId
              );

              if (isMatched) {
                stage.classList.add("visited"); // Add green highlight class
              }
            });
          }

          // Update the stage name and description for the active stage
          const activeStage = stages[0]; // You can set this dynamically based on response
          if (activeStage) {
            activeStage.style.textDecoration = "underline"; // Highlight
            stageNameElement.textContent =
              activeStage.getAttribute("data-stage");
            stageDescriptionElement.textContent =
              activeStage.getAttribute("data-description");

            markAsCurrentBtn.style.display = "block"; // Show the button
          }
        } else {
          console.error(response.message);
        }
      },
      error: function () {
        console.error("Error fetching stage data.");
      },
    });
  });

  // Add click event listeners to all stages
  stages.forEach((stage) => {
    stage.addEventListener("click", function () {
      // Reset styles and apply underline to the clicked stage
      resetStyles();
      this.style.textDecoration = "underline"; // Highlight clicked stage
      markAsCurrentBtn.style.display = "block"; // Show the button

      // Update Stage Name and Description dynamically
      const stageName = this.getAttribute("data-stage");
      const stageDescription = this.getAttribute("data-description");

      stageNameElement.textContent = stageName; // Update Stage Name
      stageDescriptionElement.textContent = stageDescription; // Update Description

      const leadId = document.querySelector("#leadid").value;
      const userId = document.querySelector("#user_id").value;
      const leadstatusid = this.getAttribute("data-stage_id");

      $.ajax({
        url: "update-lead-datetime",
        type: "POST",
        data: {
          lead_id: leadId,
          stage: stageName,
          leadstatusid: leadstatusid,
          user_id: userId,
          _csrf: yii.getCsrfToken(),
        },
        success: function (response) {
          console.log(response.changedon);
          console.log(response.visited_link);
          if (response.status === "success") {
            // Display stage datetime and duration
            document.querySelector(".stage-info-datetime").textContent =
              response.changedon;
            document.querySelector(".stage-info-duration").textContent =
              response.duration;

            // Ensure `visited_link` data exists and is an array
            if (response.visited_link && Array.isArray(response.visited_link)) {
              // Iterate through all stage elements
              document.querySelectorAll(".stage").forEach((stage) => {
                const stageId = stage.getAttribute("data-stage_id"); // Get the stage ID

                // Check if the stageId exists as a prevalue in visited_link
                const isMatched = response.visited_link.some(
                  (link) => link.prevalue === stageId
                );

                // If matched, add a class for green color
                if (isMatched) {
                  stage.classList.add("visited");
                } else {
                  stage.classList.remove("visited"); // Remove if no longer matched
                }
              });
            }
            
          } else {
            console.error(response.message);
          }
        },
        error: function () {
          console.error("Error fetching stage data.");
        },
      });
    });
  });

  // Handle "Mark as Current" button click
  markAsCurrentBtn.addEventListener("click", function () {
    // Find the currently underlined stage
    const selectedStage = document.querySelector('.stage[style*="underline"]');

    if (selectedStage) {
      // Update the active stage
      if (currentStage) {
        currentStage.classList.remove("active");
        currentStage.classList.add("visited"); // Mark as visited
      }

      selectedStage.classList.add("active");
      selectedStage.style.textDecoration = "none"; // Remove underline
      currentStage = selectedStage; // Update current stage

      // Hide the button after marking as current
      markAsCurrentBtn.style.display = "none";

      // Optional: AJAX request to update the server
      const stageValue = currentStage.getAttribute("data-stage");
      const leadstatusid = currentStage.getAttribute("data-stage_id");
      const leadId = document.querySelector("#leadid").value;
      const userId = document.querySelector("#user_id").value;
      $.ajax({
        url: "update-lead-status",
        type: "POST",
        data: {
          lead_id: leadId,
          stage: stageValue,
          leadstatusid: leadstatusid,
          user_id: userId,
          _csrf: yii.getCsrfToken(),
        },
        success: function (response) {
          console.log(response.message);
          if(response.status=='success'){
            location.reload();
          }
        
        },
      });
    }
  });
});
