var postId = 0;
var postBodyElement = null;

$('.post').find('.interaction').find('.edit').on('click', function (event) {
    event.preventDefault();

    postBodyElement = event.target.parentNode.parentNode.childNodes[1];
    var postBody = postBodyElement.textContent;
    postId = event.target.parentNode.parentNode.dataset['postid'];
    $('#post-body').val(postBody);
    $('#edit-modal').modal();
});

$('#modal-save').on('click', function () {
    $.ajax({
            method: 'POST',
            url: urlEdit,
            data: {
                body: $('#post-body').val(),
                postId: postId,
                _token: token
            }
        })
        .done(function (msg) {
            $(postBodyElement).text(msg['new_body']);
            $('#edit-modal').modal('hide');
        });
});

$('.like').on('click', function (event) {
    event.preventDefault();
    postId = event.target.parentNode.parentNode.dataset['postid'];
    var isLike = event.target.previousElementSibling == null;
    $.ajax({
            method: 'POST',
            url: urlLike,
            data: {
                isLike: isLike,
                postId: postId,
                _token: token
            }
        })
        .done(function () {
            event.target.innerText = isLike ? event.target.innerText == 'Like' ? 'You like this post' : 'Like' : event.target.innerText == 'Dislike' ? 'You don\'t like this post' : 'Dislike';
            if (isLike) {
                event.target.nextElementSibling.innerText = 'Dislike';
            } else {
                event.target.previousElementSibling.innerText = 'Like';
            }
        });
});

$("#follow_button").click(function (e) {
    e.preventDefault();

    var state = $('#follow_button').val();

    var type = "POST"; // if not follow
    var my_url = '';

    if (state == "Follow") {
        type = "POST";
        my_url = urlFollow;
    }

    if (state == "Following") {
        type = "Delete"; // unfollow
        my_url = urlDelete;
    }

    $.ajax({
            method: type,
            url: my_url,
            dataType: "json",
            data: {
                following_id: $('#following_id').val(),
                follower_id: $('#follower_id').val(),
                _token: token
            }
        })
        .done(function (msg) {
            if (state == "Follow") { //if not follow
                $('#follow_button').val('Following');
            } else {
                $('#follow_button').val('Follow');
            }
        })
        .error(function (err) {
            console.log(err);
        });
});



