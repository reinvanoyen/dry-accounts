# DRY Accounts
Account system for DRY applications

#### Installation
```ssh
composer require reinvanoyen/dry-accounts

php oak migration migrate -m accounts
```

##### Config options
Name                | Type                          | Default
------------------- | ------------------------------|--------------------------
model               | dry\orm\Model                 | Tnt\Account\Model\User
storage             | UserStorageInterface          | SessionUserStorage
factory             | UserFactoryInterface          | UserFactory
repository          | UserRepositoryInterface       | UserRepository

#### Basic example usage

##### Usage
```php
<?php

namespace controller;

class authentication
{
    public static function login(Request $request)
    {
        $login_form = new Form( $request);
        
        $login_form->add_email( 'email', [ 'required' => true ] );
        $login_form->add_password( 'password', [ 'required' => true ] );

        $auth_failed = false;
        $is_logged_in = \Tnt\Account\Facade\Auth::isAuthenticated();

        if( $login_form->validate() ) {

            if( \Tnt\Account\Facade\Auth::authenticate( $login_form->get( 'email' ), $login_form->get( 'password' ) ) ) {
                $is_logged_in = true;

            } else {
                $auth_failed = true;
            }
        }

        $tpl = new Template();
        $tpl->login_form = $login_form;
        $tpl->auth_failed = $auth_failed;
        $tpl->is_logged_in = $is_logged_in;
        $tpl->render('users/login.tpl');
    }
    
    public static function register(Request $request)
    {
        $app = Application::get();

        $register_form = new Form( $request);

        $register_failed = false;

        $register_form->add_email('email', [
            'required' => true,
            'extra_validation' => function( $value, &$errors ) use ( &$register_failed, $app )
            {
                if ($app->get(AuthenticationInterface::class)->getActivatedUser($value)) {

                    $errors[] = 'user_already_activated';
                    $register_failed = true;
                }
            }
        ]);

        $register_form->add_password( 'password', ['required' => true,] );

        if( $register_form->validate() ) {

            $user = $app->get(AuthenticationInterface::class)
                ->register(
                    $register_form->get('email'),
                    $register_form->get('password')
                );

            echo 'User registered ' . $user->email;
        }

        $tpl = new Template();
        $tpl->register_form = $register_form;
        $tpl->register_failed = $register_failed;
        $tpl->render('users/register.tpl');
    }

    public static function logout(Request $request)
    {
        \Tnt\Account\Facade\Auth::logout();

        Response::redirect('login/');
    }
}
```