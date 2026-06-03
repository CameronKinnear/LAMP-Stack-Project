function TryLogin() {
    const USER = document.getElementById('user').value;
    const PASS = document.getElementById('pass').value;

    let validLogin = true;

    // TODO: Check DB for username and password

    if (validLogin) {
        document.getElementById('login-status').style.color = 'green'
        document.getElementById('login-status').innerHTML = "Valid username and password"
        window.location.href = '/contacts.html'
        return
    } else {
        document.getElementById('login-status').style.color = 'red'
        document.getElementById('login-status').innerHTML = "Wrong username or password"
        return
    }

}