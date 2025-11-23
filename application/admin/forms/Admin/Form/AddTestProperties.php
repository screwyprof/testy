<?php

class Admin_Form_AddTestProperties extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setName('add_test_properties');

        $test_is_enabled = new Zend_Form_Element_Checkbox('test_is_enabled');
        $test_is_enabled->setLabel('Тест активен:');

        $test_title = new Zend_Form_Element_Text('test_title');
        $test_title->setLabel('Название теста:')
            ->setRequired(true)
            ->setAttrib('maxlength', '255');

        $test_desc = new Zend_Form_Element_Textarea('test_desc');
        $test_desc->setLabel('Краткое описание:')
            ->setAttrib('cols', '45')
            ->setAttrib('rows', '5');

        $test_is_mix_qst = new Zend_Form_Element_Checkbox('test_is_mix_qst');
        $test_is_mix_qst->setLabel('Перемешивать вопросы:');

        $test_is_mix_ans = new Zend_Form_Element_Checkbox('test_is_mix_ans');
        $test_is_mix_ans->setLabel('Перемешивать ответы:');

        $test_is_show_answers = new Zend_Form_Element_Checkbox('test_is_show_answers');
        $test_is_show_answers->setLabel('Показывать правильные ответы:');

        $test_qst_per_page = new Zend_Form_Element_Select('test_qst_per_page');
        $test_qst_per_page->setLabel('Отображать вопросы:')
            ->setMultiOptions(array(
                '0' => 'По одному на страницу',
                '1' => 'Все на одной странице'
            ));

        $test_time = new Zend_Form_Element_Text('test_time');
        $test_time->setLabel('Время на тест (мин.):')
            ->setRequired(true)
            ->addValidators(array(
                array('Digits'),
            ))
            ->addErrorMessage('Вы неправильно ввели время!');

        $test_qst_show_cnt = new Zend_Form_Element_Text('test_qst_show_cnt');
        $test_qst_show_cnt->setLabel('Выводимых вопросов:')
            ->setRequired(true)
            ->addValidators(array(
                array('Digits')
            ))
            ->addErrorMessage('Вы неправильно ввели данные');

        $test_start_time = new Zend_Form_Element_Text('test_start_time');
        $test_start_time->setLabel('Время начала теста (например, 17.02.2004 00:00):')
            ->setRequired(true)
            ->addValidator(new Admin_Validate_TestStartTime('test_stop_time'));

        $test_stop_time = new Zend_Form_Element_Text('test_stop_time');
        $test_stop_time->setLabel('Время окончания теста (например, 17.02.2004 00:00):')
            ->setRequired(true)
            ->addValidator(new Admin_Validate_TestStopTime('test_start_time'));

        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setLabel('Отмена');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Создать');

        $this->addElements(array(
            $test_is_enabled, $test_title, $test_desc, $test_is_mix_qst,
            $test_is_mix_ans, $test_is_show_answers, $test_qst_per_page, $test_time, $test_qst_show_cnt, $test_start_time,
            $test_stop_time, $submit, $cancel
        ));
    }
}
