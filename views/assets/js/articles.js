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
        fetch(`${form.action}`, {
            method: form.method,
            body: formData
        })
            .then(res => res.json())
            .then(data => {

                conn.publish();
                commentComponnet(id, data);
            });
    };

formsComment.forEach((e) => {
    e.addEventListener('submit', postComment);
});

conn = new ab.Session('ws://localhost:8080',
    function() {
        conn.subscribe('kittensCategory', function(topic, data) {
            // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
            console.log('New article published to category "' + topic + '" : ' + data.title);
        });
    },
    function() {
        console.warn('WebSocket connection closed');
    },
    {'skipSubprotocolCheck': true}
);
