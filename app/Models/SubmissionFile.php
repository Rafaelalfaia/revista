<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubmissionFile extends Model
{
  use HasFactory;

  protected $fillable = ['submission_id','role','disk','path','original_name','mime','size','version'];

  public function submission(){ return $this->belongsTo(Submission::class); }
}

