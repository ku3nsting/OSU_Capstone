var manageUsers = {
    init: function () {
        $('#msg-div').html('');
        $('#addUserFormBtn').click(function () {
           manageUsers.addUserForm();
        });
    },
    addUserForm: function () {
        $.ajax({
            url: '/admin/manage-users.php?action=add-user-form',
            method: 'GET'
        }).done(function (data) {
            $('#manage-users-content').html(data);
            $('#msg-div').html('');
            $('#addUserBtn').click(function () {
                manageUsers.addUser();
            });
            $('#addUserFormBtn').addClass('hidden');
            $('#manage-users-title').html('User Form');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText !== undefined) {
                $('#msg-div').html(jqXHR.responseText);
            } else {
                $('#msg-div').html(errorThrown);
            }
        });
    },
    addUser: function () {
        $.ajax({
            url: '/admin/manage-users.php',
            method: 'POST',
            data: $('#user-form').serialize()
        }).done(function (data) {
            $('#msg-div').html(data);
            // TODO: update to replace with edit form
            $('#addUserBtn').addClass('hidden');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText !== undefined) {
                $('#msg-div').html(jqXHR.responseText);
            } else {
                $('#msg-div').html(errorThrown);
            }
        });
    },
    editUserForm: function (userId) {
        $.ajax({
            url: '/admin/manage-users.php?action=edit-user-form&user-id=' + encodeURIComponent(userId),
            method: 'GET'
        }).done(function (data) {
            $('#manage-users-content').html(data);
            $('#msg-div').html('');
            $('#updateUserBtn').click(function () {
                manageUsers.updateUser();
            });
            $('#addUserFormBtn').addClass('hidden');
            $('#manage-users-title').html('User Form');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText !== undefined) {
                $('#msg-div').html(jqXHR.responseText);
            } else {
                $('#msg-div').html(errorThrown);
            }
        });
    },
    updateUser: function () {
        $.ajax({
            url: '/admin/manage-users.php',
            method: 'POST',
            data: $('#user-form').serialize()
        }).done(function (data) {
            $('#msg-div').html(data);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText !== undefined) {
                $('#msg-div').html(jqXHR.responseText);
            } else {
                $('#msg-div').html(errorThrown);
            }
        });

    }
};