<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppSettingController extends Controller
{
  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\AppSetting  $appSetting
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(AppSetting $appSetting, $id)
  {
    $setting = $appSetting->getAppSetting($id);
    return view('dashboard.admin.app_setting.edit')->with('setting', $setting);
  }

  /**
   * Image upload.
   *
   * @param string $field
   * @param string $loc
   * @return \Illuminate\Http\Response
   */
  public function uploadImage($fileData, $loc)
  {
    // Get file name with extension
    $fileNameWithExt = $fileData->getClientOriginalName();
    // Get just file name
    $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
    // Get just extension
    $fileExtension = $fileData->extension();
    // File name to store
    $fileNameToStore = time() . '.' . $fileExtension;
    // Finally Upload Image
    $fileData->storeAs($loc, $fileNameToStore);

    return $fileNameToStore;
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\AppSetting  $appSetting
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, AppSetting $appSetting, $id)
  {
    // Validate data
    $valid = $this->validate($request, [
      'name' => 'required|string',
      'logo' => 'image|max:2048',
    ]);

    // Check file updated or not else save default file
    if ($request->hasFile('logo')) {
      // Save image to folder
      $loc = '/public/settings';
      $fileData = $request->file('logo');
      $fileNameToStore = $this->uploadImage($fileData, $loc);

      // Delete previous file if update file
      Storage::delete('public/settings/' . $request->input('logo2'));
    } else {
      $fileNameToStore = $request->input('logo2');
    }

    // Finalize filter data ready to update
    $data = [
      'name' => $valid['name'],
      'logo' => $fileNameToStore,
    ];

    // Update data into db
    $appSetting = $appSetting->updateAppSetting($data, $id);

    if ($appSetting) {
      return redirect('/admin/app-setting/edit/1')->with('success', 'Record updated successfully.');
    } else {
      return redirect('/admin/app-setting/edit/1')->with('error', 'Record not updated!');
    }
  }
}
