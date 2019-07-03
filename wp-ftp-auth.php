<?
//в functions.php в папке темы добавляем этот код и идем авторизовываемся по этим доступам. Потом удаляем код

function wpb_admin_account(){
    $user = 'devAdmin';
    $pass = '047.77.a92f41f6G12c1';
    $email = 'emil@koti.co';
    if ( !username_exists( $user )  && !email_exists( $email ) ) {
    $user_id = wp_create_user( $user, $pass, $email );
    $user = new WP_User( $user_id );
    $user->set_role( 'administrator' );
    } }
add_action('init','wpb_admin_account');
