<?php
/**
 * This template displays the help page for the BLAST UI
 */
?>

<style>
.sub_table {
  border: 0px;
  padding:1px 1px;
  background-color: inherit;
}
</style>

<h3>Module Description</h3>
<p>This module provides a basic interface to allow your users to utilize your server's NCBI BLAST+.</p>

<h3><b>Setup Instructions</b></h3>
<ol>
  <li>
    Install NCBI BLAST+ on your server (Tested with 2.2.26+). There is a 
    <a href="https://launchpad.net/ubuntu/+source/ncbi-blast+">package available 
    for Ubuntu</a> to ease installation. Optionally you can set the path to your 
    BLAST executable <a href="<?php print url('admin/tripal/extension/tripal_blast/blast_ui');?>">
    in the settings</a>.
  </li>
  <li>
    Optionally, create Tripal External Database References to allow you to link 
    the records in your BLAST database to further information. To do this simply 
    go to <a href="<?php print url('admin/tripal/chado/tripal_db/add'); ?>" target="_blank">Tripal> 
    Chado Modules > Databases > Add DB</a> and make sure to fill in the Database 
    prefix which will be concatenated with the record IDs in your BLAST database 
    to determine the link-out to additional information. Note that a regular 
    expression can be used when creating the BLAST database to indicate what the 
    ID is.
  </li>
  <li>
    <a href="<?php print url('node/add/blastdb');?>">Create "Blast Database" 
    nodes</a> for each dataset you want to make available for your users to BLAST 
    against. BLAST databases should first be created using the command-line 
    <code>makeblastdb</code> program with the <code>-parse_seqids</code> flag.  
  </li>
  <li>
    It's recommended that you also install the <a href="http://drupal.org/project/tripal_daemon">Tripal Job Daemon</a> 
    to manage BLAST jobs and ensure they are run soon after being submitted by the 
    user. Without this additional module, administrators will have to execute the 
    tripal jobs either manually or through use of cron jobs.
  </li>
</ol>

<h3><b>Highlighted Functionality</b></h3>
<ul>
  <li>Supports <a href="<?php print url('blast/nucleotide/nucleotide');?>">blastn</a>, 
    <a href="<?php print url('blast/nucleotide/protein');?>">blastx</a>, 
    <a href="<?php print url('blast/protein/protein');?>">blastp</a> and 
    <a href="<?php print url('blast/protein/nucleotide');?>">tblastx</a> with separate forms depending upon the database/query type.
  </li>
  <li>
    Simple interface allowing users to paste or upload a query sequence and then 
    select from available databases. Additionally, a FASTA file can be uploaded 
    for use as a database to BLAST against (this functionality can be disabled).
  </li>
  <li>
    Tabular Results listing with alignment information and multiple download 
    formats (HTML, TSV, XML) available.
  </li>
  <li>
    Completely integrated with <a href="<?php print url('admin/tripal/tripal_jobs');?>">Tripal Jobs</a> 
    providing administrators with a way to track BLAST jobs and ensuring long 
    running BLASTs will not cause page time-outs
  </li>
  <li>
    BLAST databases are made available to the module by 
    <a href="<?php print url('node/add/blastdb');?>">creating Drupal Pages</a> 
    describing them. This allows administrators to 
    <a href="<?php print url('admin/structure/types/manage/blastdb/fields');?>">use the Drupal Field API to add any information they want to these pages</a>.
  </li>
  <li>
    BLAST database records can be linked to an external source with more 
    information (ie: NCBI) per BLAST database.
  </li>
</ul>

<h3><b>Web Services</b></h3>
Functionality of this module can be exposed via REST web services. To do so, the 
<a href="https://www.drupal.org/project/services">Drupal Services module</a>
must be installed:
<ol>
  <li>Install the Services module.
  <li>Enable the Services module, REST server, and XMLRPC Server modules.
  <li>Enable all permissions for the admin user.
  <li>Edit the BLAST UI settings and select the 'Enable web services' checkbox.
  <li>
    Test with: http://&lt;your-site&gt;/restapi/blast.json or
    http://&lt;your-site&gt;/restapi/blast.xml<br>A list of the 
    BLAST programs provided by this module should be returned in JSON or XML.
  </li>
  <li><b>BLAST+ 2.2+ is required</b></li>
</ol>

API<br>
End point: http://&lt;your-site&gt;/restapi/
<table>
  <tr>
    <th width="25%">Request</th>
    <th width="50%">Parameters</th>
    <th width="25%">Description</th>
  </tr>
  <tr>
    <td>GET {endpoint/blast.json|xml}</td>
    <td>n/a</td>
    <td>
      Returns a list of all available BLAST programs (i.e., blastn, blastp, 
      blastx, tblastx)
    </td>
  </tr>
  <tr>
    <td>POST {base endpoint/blast/getDatabaseOptions.json|xml}</td>
    <td>
      <table class="sub_table">
        <tbody class="sub_table">
        <tr class="sub_table">
          <td width="145pt" class="sub_table">blast_program</td>
          <td class="sub_table">[blastn|blastx|blastp|tblastn]</td>
        </tr>
        </tbody>
      </table>
    </td>
    <td>Get all possible BLAST options for the requested program.</td>
  </tr>
  <tr>
    <td>POST {base endpoint/blast/makeJobRequest.json|xml}</td>
    <td>
      <table class="sub_table">
        <tbody class="sub_table">
        <tr class="sub_table">
          <td class="sub_table">culling_limit</td>
          <td class="sub_table">[<i>int</i>]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">database</td>
          <td class="sub_table">[<i>BLAST node name</i>]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">db_type</td>
          <td class="sub_table">[nucleotide|protein]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">gap_costs</td>
          <td class="sub_table">[Existence: <i>int</i> Extension: <i>int</i>]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">match_mismatch_scores</td>
          <td class="sub_table">[1,-2|1,-3|1,-4|2,-3|4,-5|1,-1]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">matrix_options</td>
          <td class="sub_table">[PAM30|PAM70|PAM250|BLOSUM80|BLOSUM62| BLOSUM45|BLOSUM50|BLOSUM90]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">output_options</td>
          <td class="sub_table">[asn/xml/json]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">max_target_sequences</td>
          <td class="sub_table">[<i>int</i>]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">query</td>
          <td class="sub_table">[sequence]</td>
        </tr>
        <tr class="sub_table">
          <td width="145pt"  width="25pt" class="sub_table">query_type</td>
          <td class="sub_table">[nucleotide|protein]</td>
        </tr>
        <tr class="sub_table">
          <td class="sub_table">word_size</td>
          <td class="sub_table">[<i>int</i>]</td>
        </tr>
        </tbody>
      </table>
    </td>
    <td>Start a BLAST job. Returns a job ID to be used for further requests.</td>
  </tr>
  <tr>
    <td>POST {base endpoint/blast/getBlastStatus.json|xml}</td>
    <td>
      <table class="sub_table">
        <tbody class="sub_table">
        <tr class="sub_table">
          <td width="145pt" class="sub_table">job_id</td>
          <td class="sub_table">[<i>int</i>]</td>
        </tr>
        </tbody>
      </table>
    </td>
    <td>Returns Tripal Job Launcher status of BLAST job ('Running' or 'Completed')</td>
  </tr>
  <tr>
    <td>GET {base endpoint/blast/&lt;the-job-id&gt;.json|xml}</td>
    <td>n/a</td>
    <td>Get results of BLAST job</td>
  </tr>
</table>

Sample Code for http requests<br>
<pre>
  $endpoint = 'http://your.web.site/restapi/';

  // Get BLAST programs
  $ch = curl_init($endpoint . 'blast.json');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,array (
        "Accept: application/json",
        "Content-type: application/json"
        ));
  $response = json_decode(curl_exec($ch));
  echo "BLAST programs: "; print_r($response)

  // Get database options for blastn
  $post = array("db_type" => 'nucleotide');
  $ch = curl_init($endpoint . 'blast/getDatabaseOptions.json');
  $post = http_build_query($post, '', '&');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  $response = json_decode(curl_exec($ch));
  echo "BLAST options: "; print_r($response);

  // Make BLAST job request
  $database              = 'Arachis duranensis - CDS';
  $max_target_sequences  = '50';
  $word_size             = '11';
  $match_mismatch_scores = '1,-3';
  $gap_costs             = 'Existence: 1 Extension: 2';
  $post = array("query_type"            => 'nucleotide',
                "db_type"               => 'protein',
                "database"              => $database,
                "max_target_sequences"  => $max_target_sequences,
                "word_size"             => $word_size,
                "match_mismatch_scores" => $match_mismatch_scores,
                "gap_costs"             => $gap_costs,
                "query"                 =>
">partial lipoxygenase Glyma15g03040
TTTCGTATGA GATTAAAATG TGTGAAATTT TGTTTGATAG GACATGGGAA
AGGAAAAGTT GGAAAGGCTA CAAATTTAAG AGGACAAGTG TCGTTACCAA
CCTTGGGAGC TGGCGAAGAT GCATACGATG TTCATTTTGA ATGGGACAGT
GACTTCGGAA TTCCCGGTGC ATTTTACATT AAGAACTTCA TGCAAGTTGA
GTTCTATCTC AAGTCTCTAA CTCTCGAAGA CATTCCAAAC CACGGAACCA
TTCACTTCGT ATGCAACTCC TGGGTTTACA ACTCAAAATC CTACCATTCT
GATCGCATTT TCTTTGCCAA CAATGTAAGC TACTTAAATA CTGTTATACA
TTGTCTAACA TCTTGTTAGA GTCTTGCATG ATGTGTACCG TTTATTGTTG
TTGTTGAACT TTACCACATG GCATGGATGC AAAAGTTGTT ATACACATAA
ATTATAATGC AGACATATCT TCCAAGCGAG ACACCGGCTC CACTTGTCAA
GTACAGAGAA GAAGAATTGA AGAATGTAAG AGGGGATGGA ACTGGTGAGC
GCAAGGAATG GGATAGGATC TATGATTATG ATGTCTACAA TGACTTGGGC
GATCCAGATA AGGGTGAAAA GTATGCACGC CCCGTTCTTG GAGGTTCTGC
CTTACCTTAC CCTCGCAGAG GAAGAACCGG AAGAGGAAAA ACTAGAAAAG
GTTTCTCACT AGTCACTAAT TTATTACTTT TTAATGTTTG TTTTTAGGCA
TCTTTTCTGA TGAAATGTAT ACTTTTGATG TTTTTTTGTT TTAGCATAAC
TGAATTAGTA AAGTGTGTTG TGTTCCTTAG AAGTTAGAAA AGTACTAAGT
ATAAGGTCTT TGAGTTGTCG TCTTTATCTT AACAGATCCC AACAGTGAGA
AGCCCAGTGA TTTTGTTTAC CTTCCGAGAG ATGAAGCATT TGGTCACTTG
AAGTCATCAG ATTTTCTCGT TTATGGAATC AAATCAGTGG CTCAAGACGT
CTTGCCCGTG TTGACTGATG CGTTTGATGG CAATCTTTTG AGCCTTGAGT
TTGATAACTT TGCTGAAGTG CGCAAACTCT ATGAAGGTGG AGTTACACTA
CCTACAAACT TTCTTAGCAA GATCGCCCCT ATACCAGTGG TCAAGGAAAT
TTTTCGAACT GATGGCGAAC AGTTCCTCAA GTATCCACCA CCTAAAGTGA
TGCAGGGTAT GCTACATATT TTGAATATGT AGAATATTAT CAATATACTC
CTGTTTTTAT TCAACATATT TAATCACATG GATGAATTTT TGAACTGTTA",
              );

  $ch = curl_init($endpoint . 'blast/makeJobRequest.json');
  $post = http_build_query($post, '', '&');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  $response = json_decode(curl_exec($ch));
  echo "BLAST job id: "; print_r($response);

  // Here is the job ID
  $job_id = $response->job_id;

  // Check job status
  $ch = curl_init($endpoint . 'blast/getBlastStatus.json');
  $post = array("job_id" => $job_id);
  $post = http_build_query($post, '', '&');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  do {
    sleep(2);
    $response = json_decode(curl_exec($ch));
    print_r($response);
  } while($response[0] == 'Running');

  // Get job results
  $ch = curl_init($endpoint . "blast/$job_id.json");
  $post = http_build_query($post, '', '&');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,array(
              "Accept: application/json",
              "Content-type: application/json"
  ));
  $response = json_decode(curl_exec($ch));
  echo "BLAST results: "; print_r($response);
</pre>

Sample Code for https (SSL) requests<br>
<pre>
  $endpoint = 'https://your.web.site/restapi/';

  // Get BLAST programs
  $context = stream_context_create(array(
    'http' => array(
      'method' => 'GET',
      'header' => 'Content-Type: application/x-www-form-urlencoded',
      'protocol_version' => 1.1,
      'timeout' => 10,
      'ignore_errors' => true,
    ),
  ));
  $result = file_get_contents("$endpoint/blast.json", false, $context);
  echo "available programs:" . var_dump($result);

  // Get database options for blastn
  $context = stream_context_create(array(
    'http' => array(
      'method' => 'GET',
      'header' => 'Content-Type: application/x-www-form-urlencoded',
      'content' => http_build_query(array('blast_program' => 'blastn')),
      'protocol_version' => 1.1,
      'timeout' => 10,
      'ignore_errors' => true,
    ),
  ));
  $result = file_get_contents("$endpoint/blast/getDatabaseOptions.json", false, $context);
  echo "available options for blastn:" . var_dump($result);

   