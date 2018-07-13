public function newsong(Request $request){
		$validator = Validator::make($request->all(), [
			'song_name' => 'required',
			'song_image' => 'required|max:1999',
			'artist_id' => 'required',
			'genre_id' => 'required',
			'album_id' => 'required',
		]);
		if($validator->fails()){
			return redirect()->back()->withErrors($validator)->withInput();
		}

		if($request->hasFile('song')){
			$fileNameWithExtSong = $request->file('song')->getClientOriginalName();
			$fileNameSong = pathinfo($fileNameWithExtSong, PATHINFO_FILENAME);
			$extensionsong = $request->file('song')->getClientOriginalExtension();
			$fileNameFinalSong = $fileNameSong.'_'.time().'.'.$extensionsong;
			$path = $request->file('song')->storeAs('public/songs', $fileNameFinalSong);
		}
		else{
			$fileNameFinalSong = '';
		}

		if($request->hasFile('song_image')){
			$fileNameWithExt = $request->file('song_image')->getClientOriginalName();
			$fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
			$extension = $request->file('song_image')->getClientOriginalExtension();
			$fileNameFinal = $fileName.'_'.time().'.'.$extension;
			$path = $request->file('song_image')->storeAs('public/images/Songs', $fileNameFinal);
		}
		else{
			$fileNameFinal = 'noimage.jpg';
		}
		

		//for saving the data form the form.
		$song = new Songs;
		$song->song_name = $request->song_name;
		$song->song = $fileNameFinalSong;
		$song->song_url = $request->song_url;
		$song->song_image = $fileNameFinal;
		$song->artist_id = $request->artist_id;
		$song->genre_id = $request->genre_id;
		$song->album_id = $request->album_id;
		//$song->save();

		//for duration:
		if($song->song_url == NULL){
			$src = "/public/storage/songs/$fileNameFinalSong";
		}
		else{
			$src = $song->song_url;
		}
		
		$ffprobe = FFMpeg\FFProbe::create();
		$dur = $ffprobe->format($src)->get('duration');
		$duration = gmdate("H:i:s", $dur);
		$song->duration = $duration;
		$song->save();
		return redirect()->route('songs')->with('success', 'Song has been successfully added.');
	}
