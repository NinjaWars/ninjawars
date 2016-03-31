<?php
use NinjaWars\core\control\PasswordController;
use NinjaWars\core\data\PasswordResetRequest;
use NinjaWars\core\data\Account;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PasswordControllerTest extends PHPUnit_Framework_TestCase {
    function setUp() {
        $this->account_id = TestAccountCreateAndDestroy::account_id();
        $this->account = Account::findById($this->account_id);
        $this->nonce = nonce();
    }

    function tearDown() {
        query("delete from password_reset_requests where nonce = '777777' or nonce = :nonce", [':nonce'=>$this->nonce]);
        TestAccountCreateAndDestroy::purge_test_accounts();
    }

    private function checkTestPasswordMatches($pass) {
        $phash = query_item('select phash from accounts where account_id = :id', [':id'=>$this->account_id]);
        return password_verify($pass, $phash);
    }

    public function testRequestFormRenders() {
        // Specify email request
        $req = Request::create('/password/');
        RequestWrapper::inject($req);

        // Get a Response
        $controller = new PasswordController();
        $response = $controller->index();
        $this->assertEquals('reset.password.request.tpl', $response['template']);
    }

    public function testPostEmailCreatesAPasswordResetRequest() {
        // Craft Post Symfony Request
        $req = Request::create('/password/post_email/');
        $req->setMethod('POST');
        $req->query->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($req);

        // Pass to controller
        $controller = new PasswordController();
        $controller->postEmail();

        // reset entry should be created
        $pwrr = PasswordResetRequest::where('_account_id', '=', $this->account->id())->first();

        $this->assertNotEmpty($pwrr, 'Fail: Unable to find a matching password reset request.');
        $this->assertTrue($pwrrd instanceof PasswordResetRequest, "Request wasn't found to become a PasswordResetRequest.");
        $this->assertGreaterThan(0, $pwrr->id());
        $this->assertNotEmpty($pwrr->nonce, "Nonce/Token was blank or didn't come back.");
    }

    public function testPostEmailReturnsErrorWhenNoEmailOrNinjaName(){
        $req = Request::create('/password/post_email/');
        $req->setMethod('POST');
        RequestWrapper::inject($req);

        $controller = new PasswordController();
        $response = $controller->postEmail();
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertTrue(strpos($response->getTargetUrl(), url('email or a ninja name')) !== false, 'Url Redirection did not contain expected error string');
    }

    public function testPostEmailReturnsErrorOnUnmatchableEmailAndNinjaName(){
        $req = Request::create('/password/post_email');
        $req->setMethod('POST');
        $req->query->set('email', 'unmatchable@'.nonce().'com');
        $req->query->set('ninja_name', 'nomatch'.nonce());
        RequestWrapper::inject($req);

        $controller = new PasswordController();
        $response = $controller->postEmail();
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertTrue(strpos($response->getTargetUrl(), url('unable to find a matching account')) !== false, 'Url Redirection did not contain expected error string');
    }

    public function testPostEmailCanGetAnAccountUsingANinjaName(){
        $req = Request::create('/password/post_email/');
        $req->setMethod('POST');
        $char = TestAccountCreateAndDestroy::char();
        $ninja_name = $char->name();
        $req->query->set('ninja_name', $ninja_name);
        RequestWrapper::inject($req);

        $account = Account::findByNinjaName($ninja_name);


        $controller = new PasswordController();
        $controller->postEmail();
        // Check for a matching request for the appropriate account.
        $req = PasswordResetRequest::where('_account_id', '=', $account->id())->first();

        $this->assertNotEmpty($req, 'Fail: Unable to find a matching password reset request.');
    }

    public function testGetResetWithARandomTokenErrorRedirects(){
        $token = 'asdlfkjjklkasdfjkl';

        // Symfony Request
        $request = Request::create('/password/get_reset/');
        $request->setMethod('POST');
        $request->query->set('token', $token);
        RequestWrapper::inject($request);

        // get a response
        $controller = new PasswordController();
        $response = $controller->getReset();

        // Response should contain an array with the token in the parts.
        $this->assertTrue($response instanceof RedirectResponse, 'Error! getReset matched a garbage token!');
    }

    public function testGetResetWithAValidTokenDisplaysAFilledInPasswordResetForm() {
        $token = '4447744';

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);
        $matched_req = PasswordResetRequest::match($token);
        $this->assertNotEmpty($matched_req);

        // Symfony Request
        $request = Request::create('/password/get_reset/');
        $request->setMethod('POST');
        $request->query->set('token', $token);
        RequestWrapper::inject($request);

        // get a response
        $controller = new PasswordController();
        $response = $controller->getReset();

        // Response should contain an array with the token in the parts.
        $this->assertFalse($response instanceof RedirectResponse, 'Redirection to the url ['.($response instanceof RedirectResponse? $response->getTargetUrl() : null).'] was the invalid result of password reset.');

        $this->assertTrue(is_array($response), 'Response was not a ViewSpec Array');
        $this->assertNotEmpty($response['parts']);
        $this->assertEquals($response['parts']['token'], $token);
    }

    public function testPostResetYeildsARedirectAndAChangedPassword() {
        $token = '444555666';

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);

        $password = 'new_temp_password';

        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password);
        $request->request->set('email', $this->account->getActiveEmail());

        RequestWrapper::inject($request);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset();

        // Response should be a successful redirect
        $this->assertTrue($response instanceof RedirectResponse, 'Successful redirect after password resetting was not triggered!');
        $this->assertTrue(stripos($response->getTargetUrl(), 'message=Password') !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected message=Password url.');

        // Password should be changed.
        $this->assertTrue($this->checkTestPasswordMatches($password), 'Password was not changed!');
    }

    public function testPostResetWithBadPasswordYeildsAnError() {
        $token = '444555666';

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);

        $password = 'sh'; // Too short of a password!

        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password);
        $request->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($request);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset();

        $this->assertTrue(stripos($response->getTargetUrl(), url('not long enough')) !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected not long enough password error url.');

        // Password should be changed.
        $this->assertFalse($this->checkTestPasswordMatches($password), 'Password should not have been changed on a rejection!');
    }

    public function testPostResetWithMismatchedPasswordsYeildsError() {
        $token = '34838383838';

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);

        $password = 'legit_password_yo';
        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password.'mismatch');
        $request->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($request);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset();

        $this->assertTrue(stripos($response->getTargetUrl(), url('Password Confirmation did not match')) !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected not long enough password error url.');

        // Password should be changed.
        $this->assertFalse($this->checkTestPasswordMatches($password), 'Password should not have been changed on a rejection!');
    }

    public function testPostResetWithNoTokenYeildsAnError() {
        $token = null;

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);

        $password = 'some_new_pass';

        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password);
        $request->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($request);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset($request);

        $this->assertTrue(stripos($response->getTargetUrl(), url('No Valid')) !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected not long enough password error url.');

        // Password should be changed.
        $this->assertFalse($this->checkTestPasswordMatches($password), 'Password should not have been changed on a rejection!');
    }

    public function testPostResetWithInvalidatedTokenYeildsError() {
        $token = '34838383838';
        PasswordResetRequest::generate($this->account, $token);
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);
        $password = 'legit_password_yo';
        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password);
        $request->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($request);

        // Invalidate the token
        PasswordResetRequest::where('_account_id', '=', $this->account->id())->update(['used' => true]);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset();

        $this->assertTrue(stripos($response->getTargetUrl(), url('Token was invalid')) !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected not long enough password error url.');

        // Password should be changed.
        $this->assertFalse($this->checkTestPasswordMatches($password), 'Password should not have been changed on a rejection!');
    }
}
