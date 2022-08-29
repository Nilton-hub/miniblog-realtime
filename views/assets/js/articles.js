let ws;
const commentComponnet = (id, data) => {
    let output = document.createElement('output');
    output.style.display = 'block';
    let user = document.createElement('strong');
    let comment = document.createElement('span');

    user.innerHTML = data.username + ": ";
    comment.innerHTML = data.text;
    output.append(user, comment);
    
    document.querySelector(`div#comments-${id}`).append(output);
    return output;
};

const formsComment = document.querySelectorAll('form.form-comment'),
    postComment = (e) => {
        e.preventDefault();
        const form = e.target;
        const id = form.article_id.value;
        const formData = new FormData(form);
        console.log(form.action);
        fetch(`${form.action}`, {
            method: form.method,
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                ws.publish(form.title.value, JSON.stringify(data));
                commentComponnet(id, data);
            });
    };

function clearNotifications(id) {
    formData = new FormData();
    formData.append('identifier', id);
    fetch('http://localhost/open-notifies', {
        method: 'POST',
        body: formData
    })
        .then(res => res.text())
        .catch(error => {
            console.error(error);
        });
}

formsComment.forEach((e) => {
    e.addEventListener('submit', postComment);
});
ws = new ab.Session('ws://localhost:8080',
    function() {
        ws.subscribe('kittensCategory', function(topic, data) {
            fetch('http://localhost/views/assets/views-components/notify.php')
                .then(res => res.text())
                .then(data => {
                    
                });
        });
    },
    function() {
        console.warn('WebSocket connection closed');
    },
    {'skipSubprotocolCheck': true}
);
