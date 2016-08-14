<?php

namespace Kitano\UserActivation\Services;

use Kitano\UserActivation\Repositories\ActivationRepository as Repo;
use Illuminate\Support\Facades\Mail as Mailer;
use Kitano\UserActivation\Models\Activation;
use Carbon\Carbon;

class UserActivationService
{
    /** @var mixed */
    protected $config;

    /** The sender of the email. @var string */
    protected $from;

    /** The recipient of the email. @var string */
    protected $to;

    /** Email Subject @var string */
    protected $subject;

    /** The subject for admin notification email @var string */
    protected $admin_subject;

    /** The view for the email. @var string */
    protected $template;

    /** The view for the admin email notification @var string */
    protected $adminTemplate;

    /** The data associated with the view for the email. @var array */
    protected $data = [];

    /** The activation link for the email @var */
    protected $link;

    /** The Flag for sending notifications to admin @var bool */
    protected $sendToAdmin = false;

    /** The sender's name for the email email @var string */
    protected $from_name;

    /** Expiration time for a token in hours @var integer */
    protected $resendAfter;

    /** The Authentication model @var mixed  */
    protected $auth_model;

    /** The kind of email to send @var string */
    protected $kind;

    /** Our user object @var  */
    protected $user;

    /** Activation Repository @var Repo */
    protected $repo;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->config      = config('user_activation');
        $this->auth_model  = config('auth.providers.users.model');
        $this->resendAfter = $this->config['lifetime'];
        $this->from_name   = $this->config['from_name'];
        $this->repo        = new Repo();
    }

    /**
     * Checks if a user's token is expired and send a new one if so.
     * Otherwise, return the human readable diff between creation
     * timestamp of the token and now.
     *
     * @param $user
     *
     * @return bool|string  False if token expired
     */
    public function checkToken($user)
    {
        $activation = $this->repo->findById($user->id);

        if ($this->resendAfter != 0) {
            if ($this->shouldResend($activation)) {
                $this->recreateActivation($activation, $user);
                return false;
            }
        }

        return $this->getDiffTime($activation->created_at);
    }

    /**
     * Create an activation
     *
     * @param               $user
     */
    public function createActivation($user)
    {
        $this->user = $user;
        $token      = $this->generateToken();

        Activation::create(
            [
                'user_id'    => $this->user->id,
                'token'      => $token,
                'created_at' => new Carbon()
            ]
        );

        $this->sendEmail($token, 'send');
    }

    /**
     * Recreates an activation
     *
     * @param      $activation
     * @param null $user
     */
    public function recreateActivation($activation, $user = null)
    {
        $token      = $this->generateToken();
        $model      = new $this->auth_model;
        $this->user = is_null($user)
            ? $model->find($activation->user_id)
            : $user;

        $activation->update(
            [
                'token'      => $token,
                'created_at' => new Carbon(),
            ]
        );

        $this->sendEmail($token, 'resend');
    }

    /**
     * Send activation email to user
     *
     * @param $user
     */
    public function sendActivationEmail($user)
    {
        $this->user = $user;
        $this->sendEmail('welcome');
    }

    /**
     * Check if we should resend a fresh new token
     *
     * @param $activation
     *
     * @return bool
     */
    public function shouldResend($activation)
    {
        return $this->isTokenExpired($activation->created_at);
    }

    /**
     * Generate an url friendly 40 chars long random token
     *
     * @return string
     */
    private function generateToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    /**
     * Prepare data for the email to be sent
     *
     * @param string      $token
     * @param null|string $kind
     */
    private function sendEmail($token, $kind = null)
    {
        // making token optional
        if (func_num_args() == 1) {
            $kind = $token;
        } else {
            $this->setLink($token);
        }

        $this->kind = $kind;

        $this->setEmailParameters();

        $this->deliver();
    }

    /**
     * Set the default template for the email to be sent to admin
     */
    private function setAdminTemplate()
    {
        $this->adminTemplate = $this->config['templates']['admin_' . $this->kind];
    }

    /**
     * Set the subject for the email to be sent to the user
     */
    private function setSubject()
    {
        $this->subject = trans('activation.emails.' . $this->kind . '_subject');
    }

    /**
     * Set the subject for the email to be sent to admin
     */
    private function setAdminSubject()
    {
        $this->admin_subject = trans('activation.emails.admin_' . $this->kind . '_subject');
    }

    /**
     * Set the default template for the email to be sent to user
     */
    private function setTemplate()
    {
        $this->template = $this->config['templates'][$this->kind];
    }

    /**
     * Check if we should send email notification  to admin as well
     */
    private function shouldSendToAdmin()
    {
        $this->sendToAdmin = $this->config['admin_' . $this->kind];
    }

    /**
     * Creates a proper route for the activation link
     *
     * @param $token
     */
    private function setLink($token)
    {
        $this->link = route('auth.activate', $token);
    }

    /**
     * Delivers emails
     */
    private function deliver()
    {
        $data = $this->prepareDataForUser();

        Mailer::queue($this->template, $data, function ($message) {
            $message->from($this->from, $this->config['from_name'])
                    ->subject($this->subject)
                    ->to($this->to);
        });

        if ($this->sendToAdmin) {
            $data = $this->prepareDataForAdmin();

            Mailer::queue($this->adminTemplate, $data, function ($message) {
                $message->from($this->from, $this->config['from_system'])
                        ->subject($this->admin_subject)
                        ->to($this->config['admin_email']);
            });
        }
    }

    /**
     * Checks for Expired Activation
     *
     * @param $activation_date
     *
     * @return bool
     */
    private function isTokenExpired($activation_date)
    {
        return strtotime($activation_date) + 60 * 60 * $this->resendAfter < time();
    }

    /**
     * Prepare data to send email for user
     *
     * @return array
     */
    private function prepareDataForUser()
    {
        $user       = $this->user;
        $data       = compact('user');
        $this->to   = $this->user->email;
        $this->from = $this->config['from'];

        // include an activation link in data array if necessary
        if (isset($this->link)) {
            $data['link'] = $this->link;
        }

        return $data;
    }

    /**
     * Compose data array for easy use in admin notification email
     *
     * @return array
     */
    private function prepareDataForAdmin()
    {
        $data = $this->user->toArray();

        return compact('data');
    }

    /**
     * Set Arguments for email delivery
     */
    private function setEmailParameters()
    {
        $this->setTemplate();
        $this->setSubject();
        $this->shouldSendToAdmin();

        if ($this->sendToAdmin) {
            $this->setAdminTemplate();
            $this->setAdminSubject();
        }
    }

    /**
     * Gets Human readable time between token creation and now
     *
     * @param $created
     *
     * @return mixed
     */
    private function getDiffTime($created)
    {
        return str_replace('before', 'ago', $created->diffForHumans(Carbon::now()));
    }
}
