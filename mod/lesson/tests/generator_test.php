<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_lesson;

/**
 * Genarator tests class for mod_lesson.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {

    public function test_create_instance() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $this->assertFalse($DB->record_exists('lesson', array('course' => $course->id)));
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $records = $DB->get_records('lesson', array('course' => $course->id), 'id');
        $this->assertEquals(1, count($records));
        $this->assertTrue(array_key_exists($lesson->id, $records));

        $params = array('course' => $course->id, 'name' => 'Another lesson');
        $lesson = $this->getDataGenerator()->create_module('lesson', $params);
        $records = $DB->get_records('lesson', array('course' => $course->id), 'id');
        $this->assertEquals(2, count($records));
        $this->assertEquals('Another lesson', $records[$lesson->id]->name);
    }

    public function test_create_content() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $page1 = $lessongenerator->create_content($lesson);
        $page2 = $lessongenerator->create_content($lesson, array('title' => 'Custom title'));
        $records = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id), 'id');
        $this->assertEquals(2, count($records));
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals('Custom title', $records[$page2->id]->title);
    }

    /**
     * This tests the true/false question generator.
     */
    public function test_create_question_truefalse() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $page1 = $lessongenerator->create_question_truefalse($lesson);
        $page2 = $lessongenerator->create_question_truefalse($lesson, array('title' => 'Custom title'));
        $records = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id), 'id');
        $p1answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page1->id), 'id');
        $p2answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page2->id), 'id');
        $this->assertCount(2, $records);
        $this->assertCount(2, $p1answers); // True/false only supports 2 answer records.
        $this->assertCount(2, $p2answers);
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals($page2->title, $records[$page2->id]->title);
    }

    /**
     * This tests the multichoice question generator.
     */
    public function test_create_question_multichoice() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $page1 = $lessongenerator->create_question_multichoice($lesson);
        $page2 = $lessongenerator->create_question_multichoice($lesson, array('title' => 'Custom title'));
        $records = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id), 'id');
        $p1answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page1->id), 'id');
        $p2answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page2->id), 'id');
        $this->assertCount(2, $records);
        $this->assertCount(2, $p1answers); // Multichoice requires at least 2 records.
        $this->assertCount(2, $p2answers);
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals($page2->title, $records[$page2->id]->title);
    }

    /**
     * This tests the essay question generator.
     */
    public function test_create_question_essay() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $page1 = $lessongenerator->create_question_essay($lesson);
        $page2 = $lessongenerator->create_question_essay($lesson, array('title' => 'Custom title'));
        $records = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id), 'id');
        $p1answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page1->id), 'id');
        $p2answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page2->id), 'id');
        $this->assertCount(2, $records);
        $this->assertCount(1, $p1answers); // Essay creates a single (empty) answer record.
        $this->assertCount(1, $p2answers);
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals($page2->title, $records[$page2->id]->title);
    }

    /**
     * This tests the matching question generator.
     */
    public function test_create_question_matching() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $page1 = $lessongenerator->create_question_matching($lesson);
        $page2 = $lessongenerator->create_question_matching($lesson, array('title' => 'Custom title'));
        $records = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id), 'id');
        $p1answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page1->id), 'id');
        $p2answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page2->id), 'id');
        $this->assertCount(2, $records);
        $this->assertCount(4, $p1answers); // Matching creates two extra records plus 1 for each answer value.
        $this->assertCount(4, $p2answers);
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals($page2->title, $records[$page2->id]->title);
    }

    /**
     * This tests the numeric question generator.
     */
    public function test_create_question_numeric() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $page1 = $lessongenerator->create_question_numeric($lesson);
        $page2 = $lessongenerator->create_question_numeric($lesson, array('title' => 'Custom title'));
        $records = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id), 'id');
        $p1answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page1->id), 'id');
        $p2answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page2->id), 'id');
        $this->assertCount(2, $records);
        $this->assertCount(1, $p1answers); // Numeric only requires 1 answer.
        $this->assertCount(1, $p2answers);
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals($page2->title, $records[$page2->id]->title);
    }

    /**
     * This tests the shortanswer question generator.
     */
    public function test_create_question_shortanswer() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $page1 = $lessongenerator->create_question_shortanswer($lesson);
        $page2 = $lessongenerator->create_question_shortanswer($lesson, array('title' => 'Custom title'));
        $records = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id), 'id');
        $p1answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page1->id), 'id');
        $p2answers = $DB->get_records('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page2->id), 'id');
        $this->assertCount(2, $records);
        $this->assertCount(1, $p1answers); // Shortanswer only requires 1 answer.
        $this->assertCount(1, $p2answers);
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals($page2->title, $records[$page2->id]->title);
    }

    /**
     * This tests the cluster page generator.
     *
     * @covers ::create_cluster
     */
    public function test_create_cluster() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $cluster1 = $lessongenerator->create_cluster($lesson);
        $cluster2 = $lessongenerator->create_cluster($lesson, [
            'title' => 'Custom title',
            'contents_editor' => [
                'text' => 'Custom content',
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            ],
            'jumpto' => [LESSON_EOL],
        ]);

        $records = $DB->get_records('lesson_pages', ['lessonid' => $lesson->id], 'id');
        $c1answers = $DB->get_records('lesson_answers', ['lessonid' => $lesson->id, 'pageid' => $cluster1->id], 'id');
        $c2answers = $DB->get_records('lesson_answers', ['lessonid' => $lesson->id, 'pageid' => $cluster2->id], 'id');

        $this->assertCount(2, $records);
        $this->assertEquals($cluster1->id, $records[$cluster1->id]->id);
        $this->assertEquals(LESSON_PAGE_CLUSTER, $records[$cluster1->id]->qtype);
        $this->assertEquals($cluster2->id, $records[$cluster2->id]->id);
        $this->assertEquals(LESSON_PAGE_CLUSTER, $records[$cluster2->id]->qtype);
        $this->assertEquals($cluster2->title, $records[$cluster2->id]->title);
        $this->assertEquals('Custom content', $records[$cluster2->id]->contents);
        $this->assertCount(1, $c1answers);
        $this->assertCount(1, $c2answers);
        $this->assertEquals(LESSON_THISPAGE, array_pop($c1answers)->jumpto);
        $this->assertEquals(LESSON_EOL, array_pop($c2answers)->jumpto);
    }

    /**
     * This tests the endofcluster page generator.
     *
     * @covers ::create_endofcluster
     */
    public function test_create_endofcluster() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $endofcluster1 = $lessongenerator->create_endofcluster($lesson);
        $endofcluster2 = $lessongenerator->create_endofcluster($lesson, [
            'title' => 'Custom title',
            'contents_editor' => [
                'text' => 'Custom content',
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            ],
            'jumpto' => [LESSON_EOL],
        ]);

        $records = $DB->get_records('lesson_pages', ['lessonid' => $lesson->id], 'id');
        $eoc1answers = $DB->get_records('lesson_answers', ['lessonid' => $lesson->id, 'pageid' => $endofcluster1->id], 'id');
        $eoc2answers = $DB->get_records('lesson_answers', ['lessonid' => $lesson->id, 'pageid' => $endofcluster2->id], 'id');

        $this->assertCount(2, $records);
        $this->assertEquals($endofcluster1->id, $records[$endofcluster1->id]->id);
        $this->assertEquals(LESSON_PAGE_ENDOFCLUSTER, $records[$endofcluster1->id]->qtype);
        $this->assertEquals($endofcluster2->id, $records[$endofcluster2->id]->id);
        $this->assertEquals(LESSON_PAGE_ENDOFCLUSTER, $records[$endofcluster2->id]->qtype);
        $this->assertEquals($endofcluster2->title, $records[$endofcluster2->id]->title);
        $this->assertEquals('Custom content', $records[$endofcluster2->id]->contents);
        $this->assertCount(1, $eoc1answers);
        $this->assertCount(1, $eoc2answers);
        $this->assertEquals(LESSON_THISPAGE, array_pop($eoc1answers)->jumpto);
        $this->assertEquals(LESSON_EOL, array_pop($eoc2answers)->jumpto);
    }



    /**
     * This tests the endofbranch page generator.
     *
     * @covers ::create_endofbranch
     */
    public function test_create_endofbranch() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $endofbranch1 = $lessongenerator->create_endofbranch($lesson);
        $endofbranch2 = $lessongenerator->create_endofbranch($lesson, [
            'title' => 'Custom title',
            'contents_editor' => [
                'text' => 'Custom content',
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            ],
            'jumpto' => [LESSON_EOL],
        ]);

        $records = $DB->get_records('lesson_pages', ['lessonid' => $lesson->id], 'id');
        $eob1answers = $DB->get_records('lesson_answers', ['lessonid' => $lesson->id, 'pageid' => $endofbranch1->id], 'id');
        $eob2answers = $DB->get_records('lesson_answers', ['lessonid' => $lesson->id, 'pageid' => $endofbranch2->id], 'id');

        $this->assertCount(2, $records);
        $this->assertEquals($endofbranch1->id, $records[$endofbranch1->id]->id);
        $this->assertEquals(LESSON_PAGE_ENDOFBRANCH, $records[$endofbranch1->id]->qtype);
        $this->assertEquals($endofbranch2->id, $records[$endofbranch2->id]->id);
        $this->assertEquals(LESSON_PAGE_ENDOFBRANCH, $records[$endofbranch2->id]->qtype);
        $this->assertEquals($endofbranch2->title, $records[$endofbranch2->id]->title);
        $this->assertEquals('Custom content', $records[$endofbranch2->id]->contents);
        $this->assertCount(1, $eob1answers);
        $this->assertCount(1, $eob2answers);
        $this->assertEquals(LESSON_THISPAGE, array_pop($eob1answers)->jumpto);
        $this->assertEquals(LESSON_EOL, array_pop($eob2answers)->jumpto);
    }

    /**
     * Test create some pages and their answers.
     *
     * @covers ::create_page
     * @covers ::create_answer
     * @covers ::finish_generate_answer
     */
    public function test_create_page_and_answers() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        // Define the pages. Only a couple pages will be created since each page type has their own unit tests.
        $contentpage = [
            'title' => 'First page name',
            'content' => 'First page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
        ];
        $multichoicepage = [
            'title' => 'Multichoice question',
            'content' => 'What animal is an amphibian?',
            'qtype' => 'multichoice',
            'lessonid' => $lesson->id,
        ];

        $lessongenerator->create_page($contentpage);
        $lessongenerator->create_page($multichoicepage);

        // Check that pages haven't been generated yet because no answers were added.
        $pages = $DB->get_records('lesson_pages', ['lessonid' => $lesson->id], 'id');
        $this->assertEquals(0, count($pages));

        // Now add answers to the pages.
        $contentpagecontinueanswer = [
            'page' => $contentpage['title'],
            'answer' => 'Continue',
            'jumpto' => 'Next page',
            'score' => 1,
        ];
        $contentpagestayanswer = [
            'page' => $contentpage['title'],
            'answer' => 'Stay',
            'jumpto' => 'This page',
            'score' => 0,
        ];
        $multichoicepagefroganswer = [
            'page' => $multichoicepage['title'],
            'answer' => 'Frog',
            'response' => 'Correct answer',
            'jumpto' => 'Next page',
            'score' => 1,
        ];
        $multichoicepagecatanswer = [
            'page' => $multichoicepage['title'],
            'answer' => 'Cat',
            'response' => 'Incorrect answer',
            'jumpto' => 'This page',
            'score' => 0,
        ];
        $multichoicepagedoganswer = [
            'page' => $multichoicepage['title'],
            'answer' => 'Dog',
            'response' => 'Incorrect answer',
            'jumpto' => 'This page',
            'score' => 0,
        ];

        $lessongenerator->create_answer($contentpagecontinueanswer);
        $lessongenerator->create_answer($contentpagestayanswer);
        $lessongenerator->create_answer($multichoicepagefroganswer);
        $lessongenerator->create_answer($multichoicepagecatanswer);
        $lessongenerator->create_answer($multichoicepagedoganswer);

        // Check that pages haven't been generated yet because maybe not all answers have been added yet.
        $pages = $DB->get_records('lesson_pages', ['lessonid' => $lesson->id], 'id');
        $this->assertEquals(0, count($pages));

        // Notify that all answers have been added, so pages can be created.
        $lessongenerator->finish_generate_answer();

        // Check that pages and answers have been created.
        $pages = $DB->get_records('lesson_pages', ['lessonid' => $lesson->id], 'title DESC');
        $this->assertEquals(2, count($pages));

        $contentpagedb = array_pop($pages);
        $multichoicepagedb = array_pop($pages);
        $this->assertEquals($contentpage['title'], $contentpagedb->title);
        $this->assertEquals($contentpage['content'], $contentpagedb->contents);
        $this->assertEquals(LESSON_PAGE_BRANCHTABLE, $contentpagedb->qtype);
        $this->assertEquals($multichoicepage['title'], $multichoicepagedb->title);
        $this->assertEquals($multichoicepage['content'], $multichoicepagedb->contents);
        $this->assertEquals(LESSON_PAGE_MULTICHOICE, $multichoicepagedb->qtype);

        $answers = $DB->get_records('lesson_answers', ['lessonid' => $lesson->id], 'answer DESC');
        $this->assertEquals(5, count($answers));

        $multichoicepagecatanswerdb = array_pop($answers);
        $contentpagecontinueanswerdb = array_pop($answers);
        $multichoicepagedoganswerdb = array_pop($answers);
        $multichoicepagefroganswerdb = array_pop($answers);
        $contentpagestayanswerdb = array_pop($answers);
        $this->assertEquals($contentpagedb->id, $contentpagecontinueanswerdb->pageid);
        $this->assertEquals($contentpagecontinueanswer['answer'], $contentpagecontinueanswerdb->answer);
        $this->assertEquals(LESSON_NEXTPAGE, $contentpagecontinueanswerdb->jumpto);
        $this->assertEquals($contentpagecontinueanswer['score'], $contentpagecontinueanswerdb->score);
        $this->assertEquals($contentpagedb->id, $contentpagestayanswerdb->pageid);
        $this->assertEquals($contentpagestayanswer['answer'], $contentpagestayanswerdb->answer);
        $this->assertEquals(LESSON_THISPAGE, $contentpagestayanswerdb->jumpto);
        $this->assertEquals($contentpagestayanswer['score'], $contentpagestayanswerdb->score);
        $this->assertEquals($multichoicepagedb->id, $multichoicepagefroganswerdb->pageid);
        $this->assertEquals($multichoicepagefroganswer['answer'], $multichoicepagefroganswerdb->answer);
        $this->assertEquals($multichoicepagefroganswer['response'], $multichoicepagefroganswerdb->response);
        $this->assertEquals(LESSON_NEXTPAGE, $multichoicepagefroganswerdb->jumpto);
        $this->assertEquals($multichoicepagefroganswer['score'], $multichoicepagefroganswerdb->score);
        $this->assertEquals($multichoicepagedb->id, $multichoicepagedoganswerdb->pageid);
        $this->assertEquals($multichoicepagedoganswer['answer'], $multichoicepagedoganswerdb->answer);
        $this->assertEquals($multichoicepagedoganswer['response'], $multichoicepagedoganswerdb->response);
        $this->assertEquals(LESSON_THISPAGE, $multichoicepagedoganswerdb->jumpto);
        $this->assertEquals($multichoicepagedoganswer['score'], $multichoicepagedoganswerdb->score);
        $this->assertEquals($multichoicepagedb->id, $multichoicepagecatanswerdb->pageid);
        $this->assertEquals($multichoicepagecatanswer['answer'], $multichoicepagecatanswerdb->answer);
        $this->assertEquals($multichoicepagecatanswer['response'], $multichoicepagecatanswerdb->response);
        $this->assertEquals(LESSON_THISPAGE, $multichoicepagecatanswerdb->jumpto);
        $this->assertEquals($multichoicepagecatanswer['score'], $multichoicepagecatanswerdb->score);
    }

    /**
     * Test creating pages defining the previous pages.
     *
     * @covers ::create_page
     */
    public function test_create_page_with_previouspage() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $firstpage = [
            'title' => 'First page name',
            'content' => 'First page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
            'previouspage' => 0, // No previous page, this will be the first page.
        ];
        $secondpage = [
            'title' => 'Second page name',
            'content' => 'Second page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
            'previouspage' => 'First page name',
        ];
        $thirdpage = [
            'title' => 'Third page name',
            'content' => 'Third page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
            'previouspage' => 'Second page name',
        ];

        // Create the third page first to check that the added order is not important, the order will still be calculated right.
        $lessongenerator->create_page($thirdpage);
        $lessongenerator->create_page($firstpage);
        $lessongenerator->create_page($secondpage);

        // Don't define any answers, the default answers will be added.
        $lessongenerator->finish_generate_answer();

        $pages = $DB->get_records('lesson_pages', ['lessonid' => $lesson->id], 'title DESC');
        $this->assertEquals(3, count($pages));

        $firstpagedb = array_pop($pages);
        $secondpagedb = array_pop($pages);
        $thirdpagedb = array_pop($pages);
        $this->assertEquals($firstpage['title'], $firstpagedb->title);
        $this->assertEquals(0, $firstpagedb->prevpageid);
        $this->assertEquals($secondpagedb->id, $firstpagedb->nextpageid);
        $this->assertEquals($secondpage['title'], $secondpagedb->title);
        $this->assertEquals($firstpagedb->id, $secondpagedb->prevpageid);
        $this->assertEquals($thirdpagedb->id, $secondpagedb->nextpageid);
        $this->assertEquals($thirdpage['title'], $thirdpagedb->title);
        $this->assertEquals($secondpagedb->id, $thirdpagedb->prevpageid);
        $this->assertEquals(0, $thirdpagedb->nextpageid);
    }

    /**
     * Test creating a page with a previous page that doesn't exist.
     *
     * @covers ::create_page
     */
    public function test_create_page_invalid_previouspage() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $this->expectException('coding_exception');
        $lessongenerator->create_page([
            'title' => 'First page name',
            'content' => 'First page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
            'previouspage' => 'Invalid page',
        ]);
        $lessongenerator->finish_generate_answer();
    }

    /**
     * Test that circular dependencies are not allowed in previous pages.
     *
     * @covers ::create_page
     */
    public function test_create_page_previouspage_circular_dependency() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $this->expectException('coding_exception');
        $lessongenerator->create_page([
            'title' => 'First page name',
            'content' => 'First page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
            'previouspage' => 'Second page name',
        ]);
        $lessongenerator->create_page([
            'title' => 'Second page name',
            'content' => 'Second page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
            'previouspage' => 'First page name',
        ]);
        $lessongenerator->finish_generate_answer();
    }

    /**
     * Test creating an answer in a page that doesn't exist.
     *
     * @covers ::create_answer
     */
    public function test_create_answer_invalid_page() {
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $this->expectException('coding_exception');
        $lessongenerator->create_answer([
            'page' => 'Invalid page',
        ]);
    }

    /**
     * Test that all the possible values of jumpto work as expected when creating an answer.
     *
     * @covers ::create_answer
     */
    public function test_create_answer_jumpto() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $contentpage = [
            'title' => 'First page name',
            'content' => 'First page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
        ];
        $secondcontentpage = [
            'title' => 'Second page name',
            'content' => 'Second page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
        ];
        $thirdcontentpage = [
            'title' => 'Third page name',
            'content' => 'Third page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
        ];
        $lessongenerator->create_page($contentpage);
        $lessongenerator->create_page($secondcontentpage);
        $lessongenerator->create_page($thirdcontentpage);

        $lessongenerator->create_answer([
            'page' => $contentpage['title'],
            'answer' => 'A',
            'jumpto' => 'This page',
        ]);
        $lessongenerator->create_answer([
            'page' => $contentpage['title'],
            'answer' => 'B',
            'jumpto' => 'Next page',
        ]);
        $lessongenerator->create_answer([
            'page' => $contentpage['title'],
            'answer' => 'C',
            'jumpto' => 'Previous page',
        ]);
        $lessongenerator->create_answer([
            'page' => $secondcontentpage['title'],
            'answer' => 'D',
            'jumpto' => 'End of lesson',
        ]);
        $lessongenerator->create_answer([
            'page' => $secondcontentpage['title'],
            'answer' => 'E',
            'jumpto' => 'Unseen question within a content page',
        ]);
        $lessongenerator->create_answer([
            'page' => $secondcontentpage['title'],
            'answer' => 'F',
            'jumpto' => 'Random question within a content page',
        ]);
        $lessongenerator->create_answer([
            'page' => $thirdcontentpage['title'],
            'answer' => 'G',
            'jumpto' => 'Random content page',
        ]);
        $lessongenerator->create_answer([
            'page' => $thirdcontentpage['title'],
            'answer' => 'H',
            'jumpto' => 'Unseen question within a cluster',
        ]);
        $lessongenerator->create_answer([
            'page' => $thirdcontentpage['title'],
            'answer' => 'I',
            'jumpto' => 1234, // A page ID, it doesn't matter that it doesn't exist.
        ]);
        $lessongenerator->create_answer([
            'page' => $contentpage['title'],
            'answer' => 'J',
            'jumpto' => 'Third page name',
        ]);
        $lessongenerator->create_answer([
            'page' => $thirdcontentpage['title'],
            'answer' => 'K',
            'jumpto' => 'Second page name',
        ]);

        $lessongenerator->finish_generate_answer();

        $secondcontentpagedb = $DB->get_record('lesson_pages', ['lessonid' => $lesson->id, 'title' => $secondcontentpage['title']]);
        $thirdcontentpagedb = $DB->get_record('lesson_pages', ['lessonid' => $lesson->id, 'title' => $thirdcontentpage['title']]);
        $answers = $DB->get_records('lesson_answers', ['lessonid' => $lesson->id], 'answer DESC');
        $this->assertEquals(11, count($answers));

        $this->assertEquals(LESSON_THISPAGE, array_pop($answers)->jumpto);
        $this->assertEquals(LESSON_NEXTPAGE, array_pop($answers)->jumpto);
        $this->assertEquals(LESSON_PREVIOUSPAGE, array_pop($answers)->jumpto);
        $this->assertEquals(LESSON_EOL, array_pop($answers)->jumpto);
        $this->assertEquals(LESSON_UNSEENBRANCHPAGE, array_pop($answers)->jumpto);
        $this->assertEquals(LESSON_RANDOMPAGE, array_pop($answers)->jumpto);
        $this->assertEquals(LESSON_RANDOMBRANCH, array_pop($answers)->jumpto);
        $this->assertEquals(LESSON_CLUSTERJUMP, array_pop($answers)->jumpto);
        $this->assertEquals(1234, array_pop($answers)->jumpto);
        $this->assertEquals($thirdcontentpagedb->id, array_pop($answers)->jumpto);
        $this->assertEquals($secondcontentpagedb->id, array_pop($answers)->jumpto);
    }

    /**
     * Test invalid jumpto when creating answers.
     *
     * @covers ::create_answer
     */
    public function test_create_answer_invalid_jumpto() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $contentpage = [
            'title' => 'First page name',
            'content' => 'First page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
        ];
        $lessongenerator->create_page($contentpage);

        $lessongenerator->create_answer([
            'page' => $contentpage['title'],
            'answer' => 'Next',
            'jumpto' => 'Invalid page',
        ]);

        $this->expectException('coding_exception');
        $lessongenerator->finish_generate_answer();
    }

    /**
     * Test that circular dependencies are not allowed when creating answers.
     *
     * @covers ::create_answer
     */
    public function test_create_answer_jumpto_circular_dependency() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course));
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        $contentpage = [
            'title' => 'First page name',
            'content' => 'First page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
        ];
        $secondcontentpage = [
            'title' => 'Second page name',
            'content' => 'Second page contents',
            'qtype' => 'content',
            'lessonid' => $lesson->id,
        ];
        $lessongenerator->create_page($contentpage);
        $lessongenerator->create_page($secondcontentpage);

        $lessongenerator->create_answer([
            'page' => $contentpage['title'],
            'answer' => 'Next',
            'jumpto' => 'Second page name',
        ]);
        $lessongenerator->create_answer([
            'page' => $contentpage['title'],
            'answer' => 'Back',
            'jumpto' => 'First page name',
        ]);

        $this->expectException('coding_exception');
        $lessongenerator->finish_generate_answer();
    }

}
