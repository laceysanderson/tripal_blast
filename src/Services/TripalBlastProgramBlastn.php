<?php
/**
 * @file
 * Form definition of BLASTn program.
 */

namespace Drupal\tripal_blast\Services;

use Drupal\tripal_blast\Services\TripalBlastProgramHelper;

 /**
  * BLASTn program class.
  */
class TripalBlastProgramBlastn {
  private static $program_name;
  private static $advanced_field_names;

  /**
   * Set program_name property to BLAST program
   * blastn, blastx, blastp or tblastn.
   *
   * @param $program_name
   *   String, established program name - blastn, blastx, blastp and tblastn.
   */
  public function setProgramName($program_name) {
    if ($program_name) {
      self::$program_name = $program_name;
    }
  }

  /**
   * Adds the BLASTn Advanced Options to the passed in form.
   * This form function is meant to be called within another form definition.
   *
   * @return array
   *   Additional form field definitions.
   */  
  public function formOptions() {
    $program_name = self::$program_name;
    $form_alter = [];
    
    $container = 'ALG' ; 

    $form_alter[ $container ] = [
      '#type' => 'details',
      '#title' => t('Advanced Options'),
      '#open' => FALSE
    ];

    // @TODO: previous job details.
    $form_alter[ $container ]['general'] = [
      '#type' => 'details',
      '#title' => t('General Parameters'),
      '#open' => TRUE
    ];

      //
      // # FIELD: MAXIMUM TARGET.
      $max_target_options = TripalBlastProgramHelper::programGetMaxTarget($program_name);
      $form_alter[ $container ]['general']['maxTarget'] = [
        '#type' => 'select',
        '#title' => t('Max target sequences:'),
        '#options' => $max_target_options,
        //'#default_value' => $defaults['max_target_seqs'],
        '#description' => t('Select the maximum number of unique target sequences per 
          query sequence to show results for. Results returned may not be the highest scoring hits. 
          <a href="https://academic.oup.com/bioinformatics/article/35/9/1613/5106166" target="_blank">More Information</a>'),
      ];
      self::$advanced_field_names['maxTarget'] = [];

      //
      // # FIELD: EVAL.
      $form_alter[ $container ]['general']['eVal'] = [
        '#type' => 'textfield',
        '#title' => t('e-Value (Expected Threshold)'),
        // '#default_value' => $defaults['evalue'],
        '#size' => 12,
        '#maxlength' => 20,
        '#description' => t('Expected number of chance matches in a random model. This number should be give in a decimal format. 
          <a href="https://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#expect" target="_blank">More Information</a> | 
          <a href="https://www.youtube.com/watch?v=nO0wJgZRZJs" target="_blank">Expect value video tutorial</a>'),
      ];
      // Validate this field using number validator.
      self::$advanced_field_names['eVal'] = ['number'];

      //
      // # FIELD: WORDSIZE.
      $word_size_options = TripalBlastProgramHelper::programGetWordSize($program_name);
      $form_alter[ $container ]['general']['wordSize'] = [
        '#type' => 'select',
        '#title' => t('Word size'),
        '#options' => $word_size_options,
        //'#default_value' => $defaults['word_size'],
        '#description' => t('The length of the seed that initiates an alignment'),
      ];
      self::$advanced_field_names['wordSize'] = [];

    $form_alter[ $container ]['scoring_param'] = [
      '#type' => 'details',
      '#title' => t('Scoring parameters'),
      '#open' => TRUE,
    ];
      
      //
      // # FIELD: MATCH AND MISMATCH.
      $mm_options = TripalBlastProgramHelper::programGetMatchMismatch($program_name);
      $form_alter[ $container ]['scoring_param']['M&MScores'] = [
        '#type' => 'select',
        '#title' => t('Match/Mismatch Scores:'),
        '#options' => $mm_options,
        //'#default_value' => $defaults['matchmiss'],
        '#description' => t('Reward and penalty for matching and mismatching bases.'),
        '#ajax' => [
          'callback' => '::ajaxFieldUpdateCallback',
          'wrapper'  => 'tripal-blast-wrapper-fld-select-gap-cost',
          'method'   => 'replace',
          'effect'   => 'fade',
          'progress' => 'throbber',
          'message'  => ''
        ],
      ];
      self::$advanced_field_names['M&MScores'] = [];

      //
      // # FIELD: GAP COST.
      $mm_set = 1;      
      $gap_cost_options = TripalBlastProgramHelper::programGetGapCost($program_name, $mm_set);
      $form_alter[ $container ]['scoring_param']['gapCost'] = [
        '#type' => 'select',
        '#title' => t('Gap Costs:' . $mm_set),
        '#options' => $gap_cost_options,
        '#default_value' => $mm_set,
        '#description' => t('Cost to create and extend a gap in an alignment.'),
        '#id' => 'tripal-blast-fld-select-gap-cost',
        '#prefix' => '<div id="tripal-blast-wrapper-fld-select-gap-cost">',
        '#suffix' => '</div>',
      ];
      self::$advanced_field_names['gabCost'] = [];
  
    return $form_alter;
  }

  /**
   * Advanced field names used - refer to this value when
   * validating and submitting fields under advanced options.
   */  
  public function formFieldNames() {
    return self::$advanced_field_names;
  }

  /**
   * Map advanced options specific to this program to BLAST keywords.
   * 
   * @param $advanced_field_names
   *   Values set from form ($form_state).
   * 
   * @return array
   *   Form values mapped to BLAST keywords.
   */
  public function formFieldBlastKey($advanced_field_values) {
    $eval = $advanced_field_values['eVal'];
    $max_target = $advanced_field_values['maxTarget'];
    $word_size = $advanced_field_values['wordSize'];


    $gap = TripalBlastProgramHelper::programSetGap($advanced_field_values['gapCost']);
    $gap_open = $gap['gapOpen'];
    $gap_extend = $gap['gapExtend'];

    $mm = TripalBlastProgramHelper::programSetMatchMismatch($advanced_field_values['M&MScores']);
    $penalty = $mm['penalty'];
    $reward = $mm['reward'];

    return [
      'max_target_seqs' => $max_target,
      'evalue'   => $eval,
      'word_size' => $word_size,
      'gapopen'    => $gap_open,
      'gapextend' => $gap_extend,
      'penalty' => $penalty,
      'reward' => $reward,
    ];
  }
}