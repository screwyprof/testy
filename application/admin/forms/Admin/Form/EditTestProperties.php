<?php

class Admin_Form_EditTestProperties extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setName('edit_test_properties');

        $test_is_enabled = new Zend_Form_Element_Checkbox('test_is_enabled');
        $test_is_enabled->setLabel('Test is active:');

        $test_title = new Zend_Form_Element_Text('test_title');
        $test_title->setLabel('Test title:')
            ->setRequired(true)
            ->setAttrib('maxlength', '255');

        $test_desc = new Zend_Form_Element_Textarea('test_desc');
        $test_desc->setLabel('Short description:')
            ->setAttrib('cols', '45')
            ->setAttrib('rows', '5');

        $test_is_mix_qst = new Zend_Form_Element_Checkbox('test_is_mix_qst');
        $test_is_mix_qst->setLabel('Shuffle questions:');

        $test_is_mix_ans = new Zend_Form_Element_Checkbox('test_is_mix_ans');
        $test_is_mix_ans->setLabel('Shuffle answers:');

        $test_is_show_answers = new Zend_Form_Element_Checkbox('test_is_show_answers');
        $test_is_show_answers->setLabel('Show correct answers:');

        $test_qst_per_page = new Zend_Form_Element_Select('test_qst_per_page');
        $test_qst_per_page->setLabel('Display questions:')
            ->setMultiOptions(array(
                '0' => 'One per page',
                '1' => 'All on one page'
            ));

        $test_time = new Zend_Form_Element_Text('test_time');
        $test_time->setLabel('Test time (min.):')
            ->setRequired(true)
            ->addValidators(array(
                array('Digits'),
            ))
            ->addErrorMessage('Time format is incorrect!');

        $test_qst_show_cnt = new Zend_Form_Element_Text('test_qst_show_cnt');
        $test_qst_show_cnt->setLabel('Number of questions to display:')
            ->setRequired(true)
            ->addValidators(array(
                array('Digits')
            ))
            ->addErrorMessage('Invalid data format');

        $test_start_time = new Zend_Form_Element_Text('test_start_time');
        $test_start_time->setLabel('Test start time (e.g., 17.02.2004 00:00):')
            ->setRequired(true)
            ->addValidator(new Admin_Validate_TestStartTime('test_stop_time'));

        $test_stop_time = new Zend_Form_Element_Text('test_stop_time');
        $test_stop_time->setLabel('Test end time (e.g., 17.02.2004 00:00):')
            ->setRequired(true)
            ->addValidator(new Admin_Validate_TestStopTime('test_start_time'));

        $test_id = new Zend_Form_Element_Hidden('test_id');

        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setLabel('Cancel');
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save');

        $this->addElements(array(
            $test_is_enabled, $test_title, $test_desc, $test_is_mix_qst,
            $test_is_mix_ans, $test_is_show_answers, $test_qst_per_page, $test_time, $test_qst_show_cnt, $test_start_time,
            $test_stop_time, $test_id, $submit, $cancel
        ));
    }
}
