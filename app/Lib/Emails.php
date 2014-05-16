<?php

class Emails {
    public static function getRegisterMail($register_url, $to) {
        $email = new CakeEmail('default');
        $email->to($to);
        $email->template('email_verification');
        $email->viewVars(array('link' => $register_url,
        				  'site_name' => Configure::read('Eplite.sitename'),
                           'site_url' => Configure::read('Eplite.frameurl')));
        $email->subject('Email verification');
        $email->emailFormat('both');
        return $email;
    }
    
    public static function getInvitationMail($team_name, $invitation_url, $to) {
        $email = new CakeEmail('default');
        $email->to($to);
        $email->template('user_team_invitation');
        $email->viewVars(array('team_name' => $team_name,
        				  'invitation_url' => $invitation_url,
        				       'site_name' => Configure::read('Eplite.sitename'),
        				        'site_url' => Configure::read('Eplite.frameurl')));
        $email->subject('Team invitation');
        $email->emailFormat('both');
        return $email;
    }
}