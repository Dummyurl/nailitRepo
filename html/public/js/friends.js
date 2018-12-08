window.Friends || ( window.Friends = {} );

Friends.remove = function (friend_id, access_token) {

    $.ajax({
        type: 'POST',
        url: '/ajax/friends/method/remove',
        data: 'friend_id=' + friend_id + "&access_token=" + access_token,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            if (response.hasOwnProperty('html')) {

                $("div.js_follow_block").html(response.html);
            }
        },
        error: function(xhr, type){

        }
    });
};


Friends.sendRequest = function (profile_id, access_token) {

    $.ajax({
        type: 'POST',
        url: '/ajax/friends/method/sendRequest',
        data: 'profile_id=' + profile_id + "&access_token=" + access_token,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            if (response.hasOwnProperty('html')) {

                $("div.js_follow_block").html(response.html);
            }
        },
        error: function(xhr, type){

        }
    });
};

Friends.cancelRequest = function (profile_id, access_token) {

    $.ajax({
        type: 'POST',
        url: '/ajax/friends/method/sendRequest',
        data: 'profile_id=' + profile_id + "&access_token=" + access_token,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            $('li.card-item[data-id=' + profile_id + ']').remove();
        },
        error: function(xhr, type){

        }
    });
};

Friends.acceptRequest = function (id, friend_id, access_token) {

    $.ajax({
        type: 'POST',
        url: '/ajax/friends/method/acceptRequest',
        data: 'friend_id=' + friend_id + "&access_token=" + access_token,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            $('li.card-item[data-id=' + id + ']').remove();
        },
        error: function(xhr, type){

        }
    });
};

Friends.rejectRequest = function (id, friend_id, access_token) {

    $.ajax({
        type: 'POST',
        url: '/ajax/friends/method/rejectRequest',
        data: 'friend_id=' + friend_id + "&access_token=" + access_token,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            $('li.card-item[data-id=' + id + ']').remove();
        },
        error: function(xhr, type){

        }
    });
};

Friends.more = function (profile, offset) {

    $('button.loading-button').attr("disabled", "disabled");

    $.ajax({
        type: 'POST',
        url: '/' + profile + '/friends',
        data: 'id=' + offset + "&loaded=" + friends_loaded,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            $('header.loading-banner').remove();

            if (response.hasOwnProperty('html')){

                $("ul.content-list").append(response.html);
            }

            if (response.hasOwnProperty('banner')){

                $("div.content-list-page").append(response.banner);
            }

            friends_loaded = response.friends_loaded;
            friends_all = response.friends_all;
        },
        error: function(xhr, type){

            $('button.loading-button').removeAttr("disabled");
        }
    });
};

Friends.moreOutboxRequests = function (offset) {

    $('button.loading-button').attr("disabled", "disabled");

    $.ajax({
        type: 'POST',
        url: '/account/friends_outbox_requests',
        data: 'id=' + offset + "&loaded=" + friend_requests_loaded,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            $('header.loading-banner').remove();

            if (response.hasOwnProperty('html')){

                $("ul.content-list").append(response.html);
            }

            if (response.hasOwnProperty('banner')){

                $("div.content-list-page").append(response.banner);
            }

            friend_requests_loaded = response.friend_requests_loaded;
            friend_requests_all = response.friend_requests_all;
        },
        error: function(xhr, type){

            $('button.loading-button').removeAttr("disabled");
        }
    });
};

Friends.moreInboxRequests = function (offset) {

    $('button.loading-button').attr("disabled", "disabled");

    $.ajax({
        type: 'POST',
        url: '/account/friends_inbox_requests',
        data: 'id=' + offset + "&loaded=" + friend_requests_loaded,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            $('header.loading-banner').remove();

            if (response.hasOwnProperty('html')){

                $("ul.content-list").append(response.html);
            }

            if (response.hasOwnProperty('banner')){

                $("div.content-list-page").append(response.banner);
            }

            friend_requests_loaded = response.friend_requests_loaded;
            friend_requests_all = response.friend_requests_all;
        },
        error: function(xhr, type){

            $('button.loading-button').removeAttr("disabled");
        }
    });
};