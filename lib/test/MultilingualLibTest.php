<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alaindesilets
 * Date: 2013-09-30
 * Time: 2:05 PM
 * To change this template use File | Settings | File Templates.
 */

require_once('lib/multilingual/multilinguallib.php');

class MultilingualLibTest extends TikiTestCase
{
    public $orig_user;

    protected function setUp()
    {
        global $tikilib, $_SERVER, $user, $prefs, $multilinguallib;

        $this->orig_user = $user;

        $prefs['site_language'] = 'en';


        /* Need to set those global vars to be able to create and delete pages */
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = 'phpunit';
        $user = "user_that_can_edit";


        $page_name = "SomePage";
        $content = "This page is in English.\n" .
                   "It contains links to ((A Page That Is Already Translated)) and ((A Page That Is NOT Already Translated)).";
        $lang = 'en';
        $tikilib->create_page($page_name, 0, $content, null, '', null, $user, '', $lang);

        $page_name = "A Page That Is Already Translated";
        $content = "This page is already translated.";
        $lang = 'en';
        $tikilib->create_page($page_name, 0, $content, null, '', null, $user, '', $lang);

        $targ_page = "Une page déjà traduite";
        $targ_content = "Cette page est déjà traduite";
        $targ_lang = "fr";
        $multilinguallib->createTranslationOfPage($page_name, $lang, $targ_page, $targ_lang, $targ_content);

        $page_name = "A Page That Is NOT Already Translated";
        $content = "This page is NOT already translated.";
        $lang = 'en';
        $tikilib->create_page($page_name, 0, $content, null, '', null, $user, '', $lang);



    }

    protected function tearDown()
    {
        global $tikilib, $user;

        $tikilib->remove_all_versions("SomePage");
        $tikilib->remove_all_versions("A Page That Is Already Translated");
        $tikilib->remove_all_versions("Une page déjà traduite");
        $tikilib->remove_all_versions("A Page That Is NOT Already Translated");

        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['REQUEST_URI']);
        $user = $this->orig_user;

    }

    /**
     * @group multilingual
     * @dataProvider dataProvider_translateLinksInPageContent
     */
    public function test_translateLinksInPageContent2($src_content, $targ_lang, $exp_translated_content, $message)
    {
        global $multilinguallib;

        $got_translated_content = $multilinguallib->translateLinksInPageContent($src_content, $targ_lang);

        $this->assertEquals($exp_translated_content, $got_translated_content,
            "$message\nLinks were not properly translated in source page content.");
    }


    function dataProvider_translateLinksInPageContent()
    {
        return array(

            array("((A Page That Is Already Translated))", "fr",
                  "((Une page déjà traduite))",
                  "Case Description: Link to a page that already has a translation. The link should be ".
                  "be replaced by the link of the translation."),

            array("((A Page That Is NOT Already Translated))", "fr",
                  "{TranslationOf(src_page=\"A Page That Is NOT Already Translated\" targ_lang=fr translated_anchor_text=\"\") /}",
                  "Case Description: Link to a page that is NOT already translated. The link should be ".
                    "be replaced by a {TranslationOf} plugin."),

            array("((A Page That Is Already Translated|click here))", "fr",
                  "((Une page déjà traduite|click here))",
                  "Case Description: Link to a page that already has a translation, but with an anchor text override. ".
                  "The link should be replaced by a link to the translation, but anchor text should remain the same."),
        );

    }

}