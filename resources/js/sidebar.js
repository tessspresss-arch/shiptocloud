function normalizePath(path) {
    const clean = (path || "").split("?")[0].split("#")[0];
    return clean.length > 1 && clean.endsWith("/") ? clean.slice(0, -1) : clean;
}

function getSubmenuHeight(submenu) {
    let height = 0;
    submenu.querySelectorAll(".nav-subitem").forEach((item) => {
        height += item.offsetHeight + 4;
    });
    return height + 12;
}

function setSubmenuState(navItem, expanded) {
    const submenu = navItem.querySelector(".nav-submenu");
    const arrow = navItem.querySelector(".nav-arrow i");
    const main = navItem.querySelector(".nav-item-main");
    if (!submenu || !main) return;

    if (expanded) {
        navItem.classList.add("expanded", "active");
        main.classList.add("active");
        submenu.style.display = "flex";
        submenu.style.flexDirection = "column";
        submenu.style.maxHeight = `${getSubmenuHeight(submenu)}px`;
        submenu.style.opacity = "1";
        submenu.style.overflow = "hidden";
        main.setAttribute("aria-expanded", "true");
        if (arrow) arrow.style.transform = "rotate(90deg)";
        return;
    }

    navItem.classList.remove("expanded");
    main.classList.remove("active");
    submenu.style.maxHeight = "0";
    submenu.style.opacity = "0";
    submenu.style.overflow = "hidden";
    main.setAttribute("aria-expanded", "false");
    if (arrow) arrow.style.transform = "rotate(0deg)";

    window.setTimeout(() => {
        if (!navItem.classList.contains("expanded")) {
            submenu.style.display = "none";
        }
    }, 260);
}

function toggleSubmenu(element, evt = null) {
    if (document.body.classList.contains("sidebar-collapsed")) {
        return;
    }

    if (evt) {
        evt.preventDefault();
        evt.stopPropagation();
    }

    const navItem = element.closest(".has-submenu");
    if (!navItem) return;

    const shouldExpand = !navItem.classList.contains("expanded");

    if (shouldExpand) {
        const siblings = navItem.parentElement?.querySelectorAll(".has-submenu.expanded") || [];
        siblings.forEach((item) => {
            if (item !== navItem) {
                setSubmenuState(item, false);
            }
        });
    }

    setSubmenuState(navItem, shouldExpand);
}

window.toggleSubmenu = toggleSubmenu;

function refreshSidebarState(pathname = window.location.pathname) {
    const navRoot = document.querySelector(".sidebar-nav");
    if (!navRoot) return;

    const currentPath = normalizePath(pathname);

    navRoot.querySelectorAll(".nav-item[href], .nav-subitem").forEach((link) => {
        link.classList.remove("active");
    });

    navRoot.querySelectorAll(".has-submenu").forEach((item) => {
        item.classList.remove("active");
        setSubmenuState(item, false);
    });

    navRoot.querySelectorAll(".nav-item[href]").forEach((link) => {
        const href = normalizePath(link.getAttribute("href") || "");
        if (href && href === currentPath) {
            link.classList.add("active");
        }
    });

    navRoot.querySelectorAll(".nav-subitem").forEach((link) => {
        const href = normalizePath(link.getAttribute("href") || "");
        if (href && href === currentPath) {
            link.classList.add("active");
            const submenu = link.closest(".has-submenu");
            if (submenu) {
                setSubmenuState(submenu, true);
            }
        }
    });
}

window.refreshSidebarState = refreshSidebarState;

document.addEventListener("DOMContentLoaded", () => {
    const navRoot = document.querySelector(".sidebar-nav");
    let resizeFrame = null;
    if (!navRoot) return;

    navRoot.querySelectorAll(".has-submenu").forEach((item) => {
        setSubmenuState(item, false);
    });

    navRoot.querySelectorAll(".nav-item-main").forEach((element) => {
        element.addEventListener("click", (e) => {
            toggleSubmenu(element, e);
        });

        element.addEventListener("keydown", (e) => {
            if (e.key !== "Enter" && e.key !== " ") return;
            toggleSubmenu(element, e);
        });
    });

    refreshSidebarState(window.location.pathname);

    window.addEventListener("resize", () => {
        if (resizeFrame !== null) {
            return;
        }

        resizeFrame = window.requestAnimationFrame(() => {
            resizeFrame = null;

            if (document.body.classList.contains("sidebar-collapsed")) return;

            navRoot.querySelectorAll(".has-submenu.expanded").forEach((item) => {
                const submenu = item.querySelector(".nav-submenu");
                if (!submenu) return;
                submenu.style.maxHeight = `${getSubmenuHeight(submenu)}px`;
            });
        });
    });
});
