<?php

namespace App\Http\Controllers;

use App\Libraries\RandomObjectGeneration;
use App\Models\Default_Settings;
use App\Models\Mailing_List_User;
use App\Models\Mailing_List_User_Department_Bridge;
use App\Models\MLU_Departments;
use App\Models\Campaign;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController as Auth;
use App\Models\User;
use League\Flysystem\Exception;

class GUIController extends Controller
{
    /**
     * authRequired
     * Adds session variable for return redirect and then redirects to login page.
     *
     * @return  \Illuminate\Http\RedirectResponse
     */
    private static function authRequired() {
        \Session::put('intended',$_SERVER['REQUEST_URI']);
        return redirect()->route('login');
    }

    /**
     * returnAllTemplatesByDirectory
     * Helper function that queries template names from specific directory in file structure.
     *
     * @param   string      $directory          Directory name
     * @return  array
     */
    private static function returnAllTemplatesByDirectory($directory) {
        $files = [];
        $fileNames = [];
        $filesInFolder = \File::files("../resources/views/emails/$directory");
        foreach($filesInFolder as $path) {
            $files[] = pathinfo($path);
        }
        for($i = 0; $i < sizeof($files); $i++) {
            $fileNames[$i] = $files[$i]['filename'];
            $fileNames[$i] = substr($fileNames[$i],0,-6);
        }
        return $fileNames;
    }

    /**
     * dashboard
     * Home route.
     *
     * @return  \Illuminate\Http\RedirectResponse | \Illuminate\View\View
     */
    public static function dashboard() {
        if(Auth::check()) {
            return view("displays.dashboard");
        }
        return redirect()->route('login');
    }

    /**
     * displayCampaigns
     * Route to display all campaigns.
     *
     * @return  \Illuminate\Http\RedirectResponse | \Illuminate\View\View
     */
    public static function displayCampaigns() {
        if(Auth::check()) {
            return view("displays.showAllCampaigns");
        }
        return self::authRequired();
    }

    /**
     * displayCampaign
     * Display an individual campaign.
     *
     * @param   string      $Id
     * @return  \Illuminate\Http\RedirectResponse | \Illuminate\View\View
     */
    public static function displayCampaign($Id) {
        if(Auth::check()) {
            $campaign = Campaign::where('Id',$Id)->first();
            $user = User::where('Id',$campaign->Assignee)->first();
            $users = User::all();
            $variables = array('campaign'=>$campaign,'user'=>$user,'users'=>$users);
            return view('forms.editCampaign')->with($variables);
        }
        return self::authRequired();
    }

    /**
     * updateCampaign
     * Update the campaign (selected by the param ID) with the request object.
     *
     * @param   Request         $request
     * @param   string          $Id
     * @return  \Illuminate\Http\RedirectResponse
     */
    public static function updateCampaign(Request $request,$Id) {
        if(Auth::check()) {
            $campaign = Campaign::where('Id',$Id)->first();
            $description = $request->input('descriptionText');
            $userId = $request->input('userIdText');
            $status = $request->input('statusSelect');

            Campaign::updateCampaign($campaign, $description, $userId, $status);
            return redirect()->route('campaigns');
        }
        return redirect()->route('login');
    }

    public static function createCampaignForm() {
        if(Auth::check()) {
            $users = User::all();
            $variables = array('users'=>$users);
            return view('forms.createCampaign')->with($variables);
        }
        return self::authRequired();
    }

    public static function createCampaign(Request $request) {
        if(Auth::check()) {
            $name = $request->input('nameText');
            $description = $request->input('descriptionText');
            $assignee = $request->input('assigneeText');
            Campaign::create([
                'Name'=>$name,
                'Description'=>$description,
                'Assignee'=>$assignee,
                'Status'=>'active'
            ]);
            return redirect()->route('createCampaign');
        }
        return redirect()->route('login');
    }

    /**
     * displayTemplates
     * Route to display all templates.
     *
     * @return  \Illuminate\Http\RedirectResponse | \Illuminate\View\View
     */
    public static function displayTemplates() {
        if(Auth::check()) {
            return view("displays.showAllTemplates");
        }
        return self::authRequired();
    }

    /**
     * displayTemplate
     * Display an individual template.
     *
     * @param   string      $FileName
     * @return  \Illuminate\Http\RedirectResponse | \Illuminate\View\View
     */
    public static function displayTemplate($FileName) {
        if(Auth::check()) {
            $template = Template::where('FileName',$FileName)->first();
            if(!is_null($template)) {
                $emailType = $template->EmailType;
                $file = explode("\n",file_get_contents("../resources/views/emails/$emailType/$FileName.blade.php"));
                $variables = array('templateText'=>$file,'publicName'=>$template->PublicName,'fileName'=>$FileName);
                return view('displays.showSelectedTemplate')->with($variables);
            }
            return redirect()->route('e404');
        }
        return self::authRequired();
    }

    /**
     * generateCreateTemplate
     * Route to template creation.
     *
     * @return  \Illuminate\Http\RedirectResponse | \Illuminate\View\View
     */
    public static function generateCreateTemplate() {
        if(Auth::check()) {
            return view("forms.createNewTemplate");
        }
        return self::authRequired();
    }

    public static function displayMLUs() {
        if(Auth::check()) {
            return view("displays.showAllMLU");
        }
        return self::authRequired();
    }

    /**
     * generatePhishingEmailForm
     * Generates the Send Phishing Email Request Form in the GUI.
     *
     * @return  \Illuminate\View\View | \Illuminate\Http\RedirectResponse
     */
    public static function generatePhishingEmailForm() {
        if(Auth::check()) {
            $user = \Session::get('authUser');
            $settings = Default_Settings::where('UserId',$user->Id)->first();
            $campaigns = Campaign::all();
            $templates = self::returnAllTemplatesByDirectory('phishing');
            if(count($settings)) {
                $dft_host = $settings->MailServer;
                $dft_port = $settings->MailPort;
                $dft_user = $settings->Username;
                $dft_company = $settings->CompanyName;
            } else {
                $dft_host = '';
                $dft_port = '';
                $dft_company = '';
                $dft_user = '';
            }
            $variables = array('dft_host'=>$dft_host,'dft_port'=>$dft_port,'dft_user'=>$dft_user,
                'dft_company'=>$dft_company,'campaigns'=>$campaigns,'templates'=>$templates);
            return view('forms.phishingEmail')->with($variables);
        }
        return self::authRequired();
    }

    /**
     * generateEmailReportForm
     * Route to display the email report generation.
     *
     * @return  \Illuminate\Http\RedirectResponse | \Illuminate\View\View
     */
    public static function generateEmailReportForm() {
        if(Auth::check()) {
            $campaigns = Campaign::all();
            $users = Mailing_List_User::all();
            $variables = array('campaigns'=>$campaigns,'users'=>$users);
            return view('forms.generateEmailReport')->with($variables);
        }
        return self::authRequired();
    }

    /**
     * generateWebsiteReportForm
     * Route to display the website report generation.
     *
     * @return  \Illuminate\Http\RedirectResponse | \Illuminate\View\View
     */
    public static function generateWebsiteReportForm() {
        if(Auth::check()) {
            $campaigns = Campaign::all();
            $users = Mailing_List_User::all();
            $variables = array('campaigns'=>$campaigns,'users'=>$users);
            return view('forms.generateWebsiteReport')->with($variables);
        }
        return self::authRequired();
    }

    /**
     * updateDefaultEmailSettings
     * Post function for updating Default Email Settings, which are used to autofill the Send Email Request Form
     *
     * @param   Request         $request            Settings sent from form
     */
    public static function updateDefaultEmailSettings(Request $request) {
        $user = User::where('Username',$request->input('usernameText'))->first();
        $company = $request->input('companyText');
        $host = $request->input('mailServerText');
        $port = $request->input('mailPortText');

        $settings = Default_Settings::firstOrNew(['UserId'=>$user->Id]);
        $settings->MailServer = $host;
        $settings->MailPort = $port;
        $settings->CompanyName = $company;
        $settings->Username = $user->Username;
        $settings->save();
    }

    /**
     * generateDefaultEmailSettingsForm
     * Generates the Settings form based on input from the database.
     *
     * @return  \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public static function generateDefaultEmailSettingsForm() {
        if(Auth::check()) {
            $user = \Session::get('authUser');
            $settings = Default_Settings::where('UserId',$user->Id)->first();
            if(count($settings)) {
                $dft_host = $settings->MailServer;
                $dft_port = $settings->MailPort;
                $dft_user = $settings->Username;
                $dft_company = $settings->CompanyName;
            } else {
                $dft_host = '';
                $dft_port = '';
                $dft_company = '';
                $dft_user = '';
            }
            $variables = array('dft_host'=>$dft_host,'dft_port'=>$dft_port,'dft_user'=>$dft_user,'dft_company'=>$dft_company);
            return view('forms.defaultEmailSettings')->with($variables);
        } else {
            \Session::put('intended',route('defaultSettings')); //define route defaultSettings
            return redirect()->route('login');
        }
    }

    /**
     * createNewPhishTemplate
     * Creates new phishing template and writes it to the file structure.
     *
     * @param   Request         $request        Template name and content sent by user to create new template
     */
    public static function createNewPhishTemplate(Request $request) {
        $name = $request->input('templateName');
        $content = $request->input('templateContent');
        Template::createPhish($content,$name);
    }

    /**
     * createNewEduTemplate
     * Creates new educational template and writes it to the file structure.
     *
     * @param   Request         $request        Template name and content sent by user to create new template
     */
    public static function createNewEduTemplate(Request $request) {
        $name = $request->input('templateName');
        $content = $request->input('templateContent');
        Template::createEdu($content,$name);
    }

    /**
     * createNewCampaign
     * Creates new campaign and inserts into database.
     *
     * @param   Request         $request        Data sent by user to instantiate new campaign
     */
    public static function createNewCampaign(Request $request) {
        $user = \Session::get('authUser');
        Campaign::create(
            ['Name'=>$request->input('campaignName'),
            'Description'=>$request->input('campaignDescription'),
            'Assignee'=>$user->Id,
            'Status'=>'active']
        );
    }

    /**
     * phishHTMLReturner
     * Takes phishing template name as input and returns content of template to
     * be used as a preview in the GUI.
     *
     * @param   string      $id             Template Name
     * @return  string                      Template Content
     */
    public static function phishHTMLReturner($id) {
        try {
            if(Auth::check()) {
                $template = Template::where('Id',$id)->first();
                if(!is_null($template)) {
                    $path = "../resources/views/emails/phishing/$id.blade.php";
                    return \File::get($path);
                }
                return "Template does not exist.";
            }
            self::authRequired();
        }
        catch (Exception $e) {
            //log exception
            return "Preview Unavailable";
        }
    }

    public static function generateNewMailingListUserForm() {
        if(Auth::check()) {
            $departments = MLU_Departments::all();
            $variables = array('departments'=>$departments);
            return view('forms.addNewMLU')->with($variables);
        } else {
            \Session::put('intended',route('mailingListUser'));
            return redirect()->route('login');
        }
    }

    public static function createNewMailingListUser(Request $request) {
        $mlu = Mailing_List_User::create(
            ['Email'=>$request->input('emailText'),
                'FirstName'=>$request->input('firstNameText'),
                'LastName'=>$request->input('lastNameText'),
                'UniqueURLId'=>RandomObjectGeneration::random_str(30)]
        );
        $departments = $request->input('departmentSelect');
        foreach($departments as $department) {
            Mailing_List_User_Department_Bridge::create(
                ['UserId'=>$mlu->Id,
                    'DepartmentId'=>$department]
            );
        }
        return redirect()->route('mailingListUser');
    }

    public static function createNewMailingListDepartment(Request $request) {
        MLU_Departments::create([
            'Department'=>$request->input('departmentText')
        ]);
        return redirect()->route('mailingListDepartment');
    }

    public static function updateMailingListUser(Request $request, $Id) {
        $mlu = Mailing_List_User::where('Id',$Id)->first();
        $email = $request->input('emailText');
        $urlToggle = $request->input('urlToggle');
        $fname = $request->input('firstNameText');
        $lname = $request->input('lastNameText');
        if(!empty($urlToggle) && $urlToggle == 'on') {
            $url = RandomObjectGeneration::random_str(30);
            Mailing_List_User::updateMailingListUser($mlu,$email,$fname,$lname,$url);
        } else {
            Mailing_List_User::updateMailingListUser($mlu,$email,$fname,$lname);
        }
        return redirect()->route('mlu');
    }

    public static function generateUpdateMailingListUserForm($Id) {
        if(Auth::check()) {
            $mlu = Mailing_List_User::where('Id',$Id)->first();
            $variables = array('mlu'=>$mlu);
            return view('forms.editMLU')->with($variables);
        }
        return self::authRequired();
    }

    public static function displayMLUDs() {
        if(Auth::check()) {
            return view("displays.showAllMLUDepartments");
        }
        return self::authRequired();
    }

    public static function updateUser(Request $request) {
        if(Auth::check()) {
            $email = $request->input('emailText');
            $password = $request->input('passwordText');
            $twoFactor = $request->input('twoFactorToggle');
            if(!empty($twoFactor)) {
                $twoFactor = $twoFactor == 'true' ? true : false;
            }
            $user = \Session::get('authUser');

            User::updateUser($user, $email, password_hash($password, PASSWORD_DEFAULT), $twoFactor);
        }
    }
}
