const API_BASE = "api";

document.addEventListener("DOMContentLoaded", () => {
    setupLoginPage();
    setupContactsPage();
});

function setupLoginPage() {
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");

    if (loginForm) {
        loginForm.addEventListener("submit", handleLogin);
    }

    if (registerForm) {
        registerForm.addEventListener("submit", handleRegister);
    }
}

function setupContactsPage() {
    const contactsTableBody = document.getElementById("contacts-table-body");

    if (!contactsTableBody) {
        return;
    }

    const user = getCurrentUser();

    if (!user || !user.id) {
        window.location.href = "index.html";
        return;
    }

    document.getElementById("welcome-message").textContent =
        `Signed in as ${user.firstName} ${user.lastName}`;

    document.getElementById("logout-button").addEventListener("click", handleLogout);
    document.getElementById("search-form").addEventListener("submit", handleSearch);
    document.getElementById("show-all-button").addEventListener("click", () => searchContacts(""));
    document.getElementById("contact-form").addEventListener("submit", handleSaveContact);
    document.getElementById("cancel-edit-button").addEventListener("click", resetContactForm);

    searchContacts("");
}

async function handleLogin(event) {
    event.preventDefault();

    const loginStatus = document.getElementById("login-status");
    loginStatus.textContent = "Logging in...";
    loginStatus.className = "status-message";

    const login = document.getElementById("login-username").value.trim();
    const password = document.getElementById("login-password").value;

    const response = await postJson(`${API_BASE}/login.php`, {
        login,
        password
    });

    if (response.error || !response.id) {
        loginStatus.textContent = response.error || "Login failed.";
        loginStatus.classList.add("error");
        return;
    }

    localStorage.setItem("contactsUser", JSON.stringify({
        id: response.id,
        firstName: response.firstName,
        lastName: response.lastName
    }));

    window.location.href = "contacts.html";
}

async function handleRegister(event) {
    event.preventDefault();

    const registerStatus = document.getElementById("register-status");
    registerStatus.textContent = "Creating account...";
    registerStatus.className = "status-message";

    const firstName = document.getElementById("register-first-name").value.trim();
    const lastName = document.getElementById("register-last-name").value.trim();
    const login = document.getElementById("register-username").value.trim();
    const password = document.getElementById("register-password").value;
    const confirmPasswordInput = document.getElementById("confirmPass") || document.getElementById("register-confirm-password");

    if (confirmPasswordInput && password !== confirmPasswordInput.value) {
    	registerStatus.textContent = "Passwords do not match.";
    	registerStatus.className = "status-message error";
    	return;
    }

    const response = await postJson(`${API_BASE}/register.php`, {
        firstName,
        lastName,
        login,
        password
    });

    if (response.error || !response.id) {
        registerStatus.textContent = response.error || "Registration failed.";
        registerStatus.classList.add("error");
        return;
    }

    registerStatus.textContent = "Account created. You can now log in.";
    registerStatus.classList.add("success");

    document.getElementById("register-form").reset();

    window.location.href = "index.html";
}

function handleLogout() {
    localStorage.removeItem("contactsUser");
    window.location.href = "index.html";
}

async function handleSearch(event) {
    event.preventDefault();

    const search = document.getElementById("search-input").value.trim();
    await searchContacts(search);
}

async function searchContacts(search) {
    const user = getCurrentUser();
    const searchStatus = document.getElementById("search-status");

    searchStatus.textContent = "Searching...";
    searchStatus.className = "status-message";

    const response = await postJson(`${API_BASE}/searchContacts.php`, {
        userId: user.id,
        search
    });

    if (response.error) {
        searchStatus.textContent = response.error;
        searchStatus.classList.add("error");
        renderContacts([]);
        return;
    }

    const results = response.results || [];
    renderContacts(results);

    if (results.length === 0) {
        searchStatus.textContent = "No contacts found.";
    } else {
        searchStatus.textContent = `${results.length} contact(s) found.`;
        searchStatus.classList.add("success");
    }
}

async function handleSaveContact(event) {
    event.preventDefault();

    const user = getCurrentUser();
    const contactStatus = document.getElementById("contact-status");
    const contactId = document.getElementById("contact-id").value;

    contactStatus.textContent = "Saving contact...";
    contactStatus.className = "status-message";

    const contactData = {
        userId: user.id,
        firstName: document.getElementById("contact-first-name").value.trim(),
        lastName: document.getElementById("contact-last-name").value.trim(),
        phone: document.getElementById("contact-phone").value.trim(),
        email: document.getElementById("contact-email").value.trim()
    };

    let endpoint = `${API_BASE}/addContacts.php`;

    if (contactId) {
        endpoint = `${API_BASE}/updateContact.php`;
        contactData.contactId = parseInt(contactId, 10);
    }

    const response = await postJson(endpoint, contactData);

    if (response.error) {
        contactStatus.textContent = response.error;
        contactStatus.classList.add("error");
        return;
    }

    contactStatus.textContent = contactId ? "Contact updated." : "Contact added.";
    contactStatus.classList.add("success");

    resetContactForm();
    await searchContacts(document.getElementById("search-input").value.trim());
}

function editContact(contact) {
    document.getElementById("contact-id").value = contact.id;
    document.getElementById("contact-first-name").value = contact.firstName;
    document.getElementById("contact-last-name").value = contact.lastName;
    document.getElementById("contact-phone").value = contact.phone;
    document.getElementById("contact-email").value = contact.email;

    document.getElementById("contact-form-title").textContent = "Edit Contact";
    document.getElementById("save-contact-button").textContent = "Update Contact";
    document.getElementById("save-contact-button").style.width = "25%"
    document.getElementById("cancel-edit-button").style.display = "inline-block";
    document.getElementById("contact-status").textContent = "";
}

async function deleteContact(contactId) {
    const user = getCurrentUser();

    const confirmed = confirm("Delete this contact?");
    if (!confirmed) {
        return;
    }

    const response = await postJson(`${API_BASE}/deleteContact.php`, {
        userId: user.id,
        contactId
    });

    const contactStatus = document.getElementById("contact-status");
    contactStatus.className = "status-message";

    if (response.error || !response.success) {
        contactStatus.textContent = response.error || "Delete failed.";
        contactStatus.classList.add("error");
        return;
    }

    contactStatus.textContent = "Contact deleted.";
    contactStatus.classList.add("success");

    await searchContacts(document.getElementById("search-input").value.trim());
}

function resetContactForm() {
    document.getElementById("contact-form").reset();
    document.getElementById("contact-id").value = "";
    document.getElementById("contact-form-title").textContent = "Add Contact";
    document.getElementById("save-contact-button").textContent = "Add Contact";
    document.getElementById("cancel-edit-button").style.display = "none";
    document.getElementById("save-contact-button").style.width = "20%"
}

function renderContacts(contacts) {
    const tableBody = document.getElementById("contacts-table-body");
    tableBody.innerHTML = "";

    if (!contacts || contacts.length === 0) {
        const row = document.createElement("tr");
        row.innerHTML = `<td colspan="4">No contacts found.</td>`;
        tableBody.appendChild(row);
        return;
    }

    contacts.forEach((contact) => {
        const row = document.createElement("tr");
        row.className = 'table-row';

        const fullName = `${escapeHtml(contact.firstName)} ${escapeHtml(contact.lastName)}`.trim();

        row.innerHTML = `
            <td>${fullName}</td>
            <td>${escapeHtml(contact.phone)}</td>
            <td>${escapeHtml(contact.email)}</td>
            <td>
                <button type="button" class="small-button edit-button">Edit</button>
                <button type="button" class="small-button danger-button delete-button">Delete</button>
            </td>
        `;

        row.querySelector(".edit-button").addEventListener("click", () => editContact(contact));
        row.querySelector(".delete-button").addEventListener("click", () => deleteContact(contact.id));

        tableBody.appendChild(row);
    });
}

async function postJson(url, data) {
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        return await response.json();
    } catch (error) {
        return {
            error: "Network or server error."
        };
    }
}

function getCurrentUser() {
    const rawUser = localStorage.getItem("contactsUser");

    if (!rawUser) {
        return null;
    }

    try {
        return JSON.parse(rawUser);
    } catch {
        return null;
    }
}

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}