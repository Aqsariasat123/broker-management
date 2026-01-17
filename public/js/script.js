// Update toggle button icon based on sidebar state
function updateToggleButtonIcon() {
  const sidebar = document.querySelector(".sidebar");
  const toggleBtn = document.getElementById("toggleBtn");
  if (!toggleBtn) return;

  const openIcon = toggleBtn.querySelector(".toggle-icon-open");
  const closeIcon = toggleBtn.querySelector(".toggle-icon-close");

  if (window.innerWidth > 768) {
    // Desktop: collapsed = closed, not collapsed = open
    if (sidebar.classList.contains("collapsed")) {
      // Sidebar is collapsed (closed) - show open icon
      if (openIcon) openIcon.style.display = "inline";
      if (closeIcon) closeIcon.style.display = "none";
    } else {
      // Sidebar is open - show close icon
      if (openIcon) openIcon.style.display = "none";
      if (closeIcon) closeIcon.style.display = "inline";
    }
  } else {
    // Mobile: active = open, not active = closed
    if (sidebar.classList.contains("active")) {
      // Sidebar is open - show close icon
      if (openIcon) openIcon.style.display = "none";
      if (closeIcon) closeIcon.style.display = "inline";
    } else {
      // Sidebar is closed - show open icon
      if (openIcon) openIcon.style.display = "inline";
      if (closeIcon) closeIcon.style.display = "none";
    }
  }
}

// Update toggleSidebar function - now toggles collapsed state instead of hiding
function toggleSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const body = document.body;

  // On desktop: toggle collapsed state (icons only)
  if (window.innerWidth > 768) {
    sidebar.classList.toggle("collapsed");
    // Update main content margin
    if (sidebar.classList.contains("collapsed")) {
      body.classList.add("sidebar-collapsed");
      // Initialize tooltip positioning after sidebar collapses
      setTimeout(() => {
        initTooltips();
      }, 100);
    } else {
      body.classList.remove("sidebar-collapsed");
      // Remove any visible tooltips
      const tooltip = document.querySelector(".sidebar-tooltip-chip");
      if (tooltip) {
        tooltip.remove();
      }
    }
  } else {
    // On mobile: keep original hide/show behavior
    sidebar.classList.toggle("active");
    if (sidebar.classList.contains("active")) {
      body.classList.add("sidebar-open");
    } else {
      body.classList.remove("sidebar-open");
    }
  }

  // Update toggle button icon
  updateToggleButtonIcon();
}

// Initialize tooltip positioning when sidebar is collapsed
function initTooltips() {
  const sidebar = document.querySelector(".sidebar");
  if (!sidebar) return;

  const menuItems = sidebar.querySelectorAll(".ks-sidebar-menu li[data-tooltip]");                                                                              

  menuItems.forEach((item) => {
    // Skip if already initialized
    if (item.hasAttribute('data-tooltip-initialized')) {
      return;
    }
    item.setAttribute('data-tooltip-initialized', 'true');

    item.addEventListener("mouseenter", function(e) {
      if (!sidebar.classList.contains("collapsed")) return;

      const tooltip = this.getAttribute("data-tooltip");
      if (!tooltip) return;

      // Remove any existing tooltip
      const existingTooltip = document.querySelector(".sidebar-tooltip-chip");  
      if (existingTooltip) {
        existingTooltip.remove();
      }

      const rect = this.getBoundingClientRect();

      // Create tooltip element
      const tooltipEl = document.createElement("div");
      tooltipEl.className = "sidebar-tooltip-chip";
      tooltipEl.textContent = tooltip;
      document.body.appendChild(tooltipEl);

      // Position tooltip to the right of the icon
      const left = rect.right + 8;
      const top = rect.top + (rect.height / 2);

      tooltipEl.style.left = left + "px";
      tooltipEl.style.top = top + "px";
      tooltipEl.style.transform = "translateY(-50%)";
      tooltipEl.style.opacity = "0";

      // Trigger animation
      requestAnimationFrame(() => {
        tooltipEl.style.opacity = "1";
        tooltipEl.style.transform = "translateY(-50%) translateX(0)";
      });
    });

    item.addEventListener("mouseleave", function() {
      const tooltipEl = document.querySelector(".sidebar-tooltip-chip");        
      if (tooltipEl) {
        tooltipEl.style.opacity = "0";
        tooltipEl.style.transform = "translateY(-50%) translateX(-5px)";        
        setTimeout(() => {
          if (tooltipEl.parentNode) {
            tooltipEl.remove();
          }
        }, 200);
      }
    });
  });
}

// Initialize sidebar state on page load
document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".sidebar");

  // Set initial state based on screen size
  if (window.innerWidth <= 768) {
    sidebar.classList.remove("active");
  } else {
    sidebar.classList.remove("active");
  }

  // Setup toggle button
  const toggleBtn = document.getElementById("toggleBtn");
  if (toggleBtn) {
    toggleBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      toggleSidebar();
    });
  }

  // Initialize tooltips if sidebar is collapsed
  if (sidebar && sidebar.classList.contains("collapsed")) {
    setTimeout(() => {
      initTooltips();
    }, 100);
  }

  // Initialize toggle button icon
  updateToggleButtonIcon();
});

// Close sidebar when clicking outside
document.addEventListener("click", (e) => {
  const sidebar = document.querySelector(".sidebar");
  const toggleBtn = document.getElementById("toggleBtn");

  if (window.innerWidth <= 768) {
    if (
      sidebar.classList.contains("active") &&
      !sidebar.contains(e.target) &&
      !toggleBtn.contains(e.target)
    ) {
      sidebar.classList.remove("active");
      document.body.classList.remove("sidebar-open");
    }
  }
});

// Handle window resize
window.addEventListener("resize", () => {
  const sidebar = document.querySelector(".sidebar");

  if (window.innerWidth > 768) {
    sidebar.classList.remove("active");
    document.body.classList.remove("sidebar-open");
  } else {
    sidebar.classList.remove("collapsed");
    document.body.classList.remove("sidebar-collapsed");
    // Remove any visible tooltips
    const tooltip = document.querySelector(".sidebar-tooltip-chip");
    if (tooltip) {
      tooltip.remove();
    }
  }

  // Update toggle button icon after resize
  updateToggleButtonIcon();
});

// Profile dropdown functionality
document.addEventListener("DOMContentLoaded", () => {
  const profileBtn = document.getElementById("profileBtn");
  const profileDropdown = document.querySelector(".profile-dropdown");

  if (profileBtn && profileDropdown) {
    profileBtn.addEventListener("click", function(e) {
      e.stopPropagation();
      profileDropdown.classList.toggle("active");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function(e) {
      if (!profileDropdown.contains(e.target)) {
        profileDropdown.classList.remove("active");
      }
    });

    // Close dropdown on escape key
    document.addEventListener("keydown", function(e) {
      if (e.key === "Escape" && profileDropdown.classList.contains("active")) {
        profileDropdown.classList.remove("active");
      }
    });
  }
});

// Sidebar Collapse Button (inside sidebar)
document.addEventListener("DOMContentLoaded", () => {
  const sidebarCollapseBtn = document.getElementById("sidebarCollapseBtn");
  const sidebar = document.querySelector(".sidebar");
  const body = document.body;

  if (sidebarCollapseBtn && sidebar) {
    // Default: sidebar is expanded (not collapsed)
    // Only collapse if user manually collapsed it in this session
    sidebar.classList.remove("collapsed");
    body.classList.remove("sidebar-collapsed");

    sidebarCollapseBtn.addEventListener("click", function(e) {
      e.stopPropagation();
      sidebar.classList.toggle("collapsed");

      if (sidebar.classList.contains("collapsed")) {
        body.classList.add("sidebar-collapsed");
        setTimeout(() => initTooltips(), 100);
      } else {
        body.classList.remove("sidebar-collapsed");
        const tooltip = document.querySelector(".sidebar-tooltip-chip");
        if (tooltip) tooltip.remove();
      }

      updateToggleButtonIcon();
    });
  }
});

