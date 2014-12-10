
function C_fileQueued(file) {
}


function _warn( msg )
{
  if( swfu.customSettings.error != ''  ){
     $('#'+swfu.customSettings.error).html( msg );
	 $('#'+swfu.customSettings.error).removeClass("hide");
  }
  else{
   alert(msg );
  }
}

function C_fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			_warn("您正在上传的文件队列过多.\n" + (message === 0 ? "您已达到上传限制" : "您最多能选择 " + (message > 1 ? "上传 " + message + " 文件." : "一个文件.")));
			return;
		}
		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			_warn("错误代码: 文件尺寸过大, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		   _warn("错误代码: 零字节文件, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			_warn("错误代码: 不支持的文件类型, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		default:
			if (file !== null) {
			}
			 _warn("错误代码: " + errorCode + ", 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function C_fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
	   if( numFilesQueued > 0 )
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

function C_uploadStart(file) 
{
	this.setButtonDisabled(true);
	return true;
}

function C_uploadProgress(file, bytesLoaded, bytesTotal) {
}

function C_uploadSuccess(file, serverData) 
{ 
	 serverData = eval('(' + serverData + ')'); 
     if( serverData  && "undefined" != typeof(serverData.rcode) )
	 {
	   if( parseInt( serverData.rcode ))
	   {
		    $('#'+swfu.customSettings.error).addClass('hide');
		  if( swfu.customSettings.target != '')
		 {
			  var divtarget = document.getElementById( swfu.customSettings.target );
		        divtarget.value = serverData.file_path ;
		 }
		 if( swfu.customSettings.preview != ''){
			var divpreview = document.getElementById( swfu.customSettings.preview );
		     divpreview.src = serverData.view_path ;
		 }
	   }
	   else
	  {
	    if( swfu.customSettings.error != '')
		{
		   $('#'+swfu.customSettings.error).html( serverData.msg );
		   $('#'+swfu.customSettings.error).show();
		}
		else{
		 _warn( serverData.msg );
		}
	  }
	 }
	 this.setButtonDisabled( false );
}

function C_uploadError(file, errorCode, message) {
	this.setButtonDisabled( false );
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			_warn("错误代码: HTTP错误, 文件名: " + file.name + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			_warn("错误代码: 上传失败, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			_warn("错误代码: IO 错误, 文件名: " + file.name + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			_warn("错误代码: 安全错误, 文件名: " + file.name + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			_warn("错误代码: 超出上传限制, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			_warn("错误代码: 文件验证失败, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			if (this.getStats().files_queued === 0) {
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			_warn("停止");
			break;
		default:
			_warn("错误代码: " + errorCode + ", 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function C_uploadComplete(file) {
	if (this.getStats().files_queued === 0) {
	}
}

// This event comes from the Queue Plugin
function C_queueComplete(numFilesUploaded) {
}
