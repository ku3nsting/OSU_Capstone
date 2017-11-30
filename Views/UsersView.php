<?php
/**
 * User: jmueller
 * Date: 28/10/17
 * Time: 2:44 PM
 */

namespace views;


use controllers\UsersController;

class UsersView
{
    /**
     * @param $users
     * @param $offset
     * @param $userCount
     * @return string
     */
    public static function indexView($users, $offset, $userCount)
    {
        $userTable = self::usersTable($users, $offset, $userCount);

        return '<div class="row">
            <h2 class="col-sm-9" id="manage-users-title">Manage Users</h2>
            <div class="col-sm-2 pull-right">
                <a href="#" id="addUserFormBtn" class="btn btn-primary" style="margin-top: 20px">Add User Form</a>
            </div>
        </div>
        <div id="msg-div"></div>
        <div id="manage-users-content">
            ' . $userTable . '
        </div>
        ';
    }

    /**
     * @param $users
     * @param $offset
     * @param $userCount
     * @return string
     */
    public static function usersTable($users, $offset, $userCount)
    {
        if (empty($users)) {
            return "<div class='alert alert-info'>No users in database</div>";
        }

        // create the users tables rows
        $userRows = '';
        foreach ($users as $user) {
            $userRows .= "<tr onclick='manageUsers.editUserForm(" . html($user['ID']) . ")' style='cursor: pointer;'>
                <td>" . html($user['ID']) . "</td>
                <td>" . html($user['fullName']) . "</td>
                <td>" . html($user['hireDate']) . "</td>
                <td>" . html(ucfirst($user['Type'])) . "</td>
            </tr>";
        }

        $prev = $offset - 15;
        $prevClass = $prev < 0 ? 'hidden' : '';
        $next = $offset + 15;
        $nextClass = $next > $userCount ? 'hidden' : '';

        // create the users table
        return "
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th>User Id</th>
                        <th>Employee Name</th>
                        <th>Hire Date (YYYY-MM-DD)</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>$userRows</tbody>
            </table>
            <nav aria-label=''>
              <ul class='pager'>
                <li class='$prevClass'><a href='javascript: void(0);' onclick='manageUsers.changePage($prev)' >Previous</a></li>
                <li class='$nextClass'><a href='javascript: void(0);' onclick='manageUsers.changePage($next)'>Next</a></li>
              </ul>
            </nav>
        ";
    }

    /**
     * @param array $user
     * @return string
     */
    public static function userForm($user = [])
    {
        if (!empty($user)) {
            $action = 'edit-user';
            $fName = html($user['fName']);
            $lName = html($user['lName']);
            $hireDate = html($user['hireDate']);
            $Email = html($user['Email']);
            $Type = html($user['Type']);
            $Bio = html($user['Bio']);
            $user['ID'] = html($user['ID']);
        } else {
            $action = 'add-user';
            $fName = $lName = $hireDate = $Email = $Type = $Bio = '';
        }

        if (!empty($user['signFile'])) {
            $signFileHtml = "
                <div class='form-group' id='signature-div'>
                    <label for='signature'>Signature</label>
                    <div>
                        <img src='" . html($user['signFile']) . '?' . time() . "' style='max-height: 100px;'>
                        <span class='glyphicon glyphicon-remove' style='color: darkred;' onclick='manageUsers.deleteSignature();'></span>
                    </div>
                </div>";
        } else {
            $signFileHtml = self::userSignatureFormField();
        }

        if (!empty($user['profilePhoto'])) {
            $profilePhotoHtml = "
                <div class='form-group' id='profile-photo-div'>
                    <label for='profilePhoto'>Profile Photo</label>
                    <div>
                        <img src='" . html($user['profilePhoto']) . '?' . time() . "' style='max-height: 100px;'>
                        <span class='glyphicon glyphicon-remove' style='color: darkred;' onclick='manageUsers.deleteProfilePhoto();'></span>
                    </div>
                </div>";
        } else {
            $profilePhotoHtml = self::userProfilePhotoFormField();
        }

        return "
            <form id='user-form' enctype='multipart/form-data'>
                <input class='hidden' id='action' name='action' value='$action'>
                " . (!empty($user['ID']) ? "<input class='hidden' id='userId' name='userId' value='{$user['ID']}'>" : '') . "
                <div class='form-group'>
                     <label for='fName'>First Name</label>           
                     <input type='text' id='fName' name='fName' class='form-control' value='$fName'>
                </div>
                <div class='form-group'>
                     <label for='lName'>Last Name</label>           
                     <input type='text' id='lName' name='lName' class='form-control' value='$lName'>
                </div>
                <div class='form-group'>
                     <label for='Email'>Email</label>           
                     <input type='email' id='Email' name='Email' class='form-control' value='$Email'>
                </div>
                <div class='form-group'>
                     <label for='hireDate'>Hire Date</label>           
                     <input type='date' id='hireDate' name='hireDate' class='form-control' value='$hireDate'>
                </div>" .

                // Only add password for new users
                (empty($user)
                    ? "<div class='form-group'>
                         <label for='Password'>Password</label>
                         <input type='password' id='Password' name='Password' class='form-control'>
                    </div>
                    <div class='form-group'>
                         <label for='Password'>Re-Enter Password</label>
                         <input type='password' id='PasswordAgain' name='PasswordAgain' class='form-control'>
                    </div>
                    "
                    : ''
                ) .

                (!empty($user) ? $signFileHtml . $profilePhotoHtml
                    : "<div class='alert alert-info'>Please create user first, then add signature file and profile photo</div>"
                )

                . "
                <div class='form-group'>
                     <label for='Type'>Type</label>           
                     <select id='Type' name='Type' class='form-control'>
                        <option value='' " . ($Type === '' ? 'selected' : '') . ">Please Select ...</option>
                        <option value='user' " . ($Type === 'user' ? 'selected' : '') . ">Normal User</option>
                        <option value='admin' " . ($Type === 'admin' ? 'selected' : '') . ">Admin User</option>
                     </select>
                </div>
                <div class='form-group'>
                     <label for='Bio'>Bio</label>
                     <textarea id='Bio' name='Bio' class='form-control'>$Bio</textarea>
                </div>
                " . ($action === 'add-user'
                    ? "<a class='btn btn-primary' href='#' role='button' id='addUserBtn'>Add User</a>"
                    : "<a class='btn btn-primary' href='#' role='button' id='updateUserBtn'>Update User</a>"
                ) . "
                " . (empty($user['awardCount']) && !empty($user['ID'])
                    ? "<a class='btn btn-danger' href='#' role='button' id='deleteUserBtn'>Delete</a>"
                    : ''
                ) . "
            </form>
        ";
    }

    /**
     * @return string
     */
    public static function userSignatureFormField()
    {
        return "
                <div class='form-group' id='signature-div'>
                    <label for='signature'>Signature File Upload (" . implode(',', UsersController::$signFileTypes) . ")</label>
                    <input type='file' id='signature' name='signature'>
                </div>";
    }

    public static function userProfilePhotoFormField()
    {
        return "
            <div class='form-group' id='signature-div'>
                <label for='profilePhoto'>Profile Photo File Upload (" . implode(',', UsersController::$profilePhotoTypes) . ")</label>
                <input type='file' id='profilePhoto' name='profilePhoto'>
            </div>";
    }
}