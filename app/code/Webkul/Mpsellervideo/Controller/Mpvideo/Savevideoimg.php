<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsellervideo
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsellervideo\Controller\Mpvideo;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;


/**
 *  Webkul Mpsellervideo Mpvideo Savevideo controller
 */
class Savevideoimg extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;
    /**
     * File Uploader factory.
     *
     * @var UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var boolean
     */
    protected $_error = false;

    /**
     * @var \Webkul\Mpsellervideo\Helper\Data
     */
    protected $helper;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $sellerCollection;

    /**
     * @var \Webkul\Marketplace\Model\Seller
     */
    protected $sellerModel;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     */
    public function __construct(Context $context, Session $customerSession, FormKeyValidator $formKeyValidator, Filesystem $filesystem, UploaderFactory $fileUploaderFactory, \Webkul\Mpsellervideo\Helper\Data $helper, \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerCollection, \Webkul\Marketplace\Model\Seller $sellerModel)
    {
        $this->_session = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->helper = $helper;
        $this->sellerCollection = $sellerCollection;
        $this->sellerModel = $sellerModel;
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param  RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Model\Url')->getLoginUrl();

        if (!$this->_session->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * To save video images and video settings in DB.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $pro_array=array();

            $id = $this->getRequest()->getParam('video_id');
            if ($id > 0) {
                return $this->updateRecord($data, $pro_array, $id);
            } else {
                return $this->addNewRecord($data, $pro_array, $id);

            }
            return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
        }

        $this->messageManager->addError(__('Unable to find Video to save'));
        // $this->_redirect('*/*/');
        return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);


    }

    protected function updateRecord($data, $pro_array, $id)
    {
        $post_max_size = $this->parse_size(ini_get('post_max_size'));//echo "<br/>";
        $upload_max_size = $this->parse_size(ini_get('upload_max_filesize'));//echo "<br/>";
        $upload_max_size_def = ini_get('upload_max_filesize');//echo "<br/>";
        $post_max_size_def = ini_get('post_max_size');//echo "<br/>";
        $time = time() . '_';
        $path = $this->_objectManager->get('\Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'productvideos/files/' . $time;
        if (isset($_FILES['videoimg_0']['name']) && $_FILES['videoimg_0']['name'] != '') {
            /*Match get size*/
            $filesize = $this->formatSizeUnits($_FILES['videoimg_0']['size']);
            /*Match size allowed*/
            if ($_FILES['videoimg_0']['size'] > $post_max_size || $_FILES['videoimg_0']['size'] > $upload_max_size) {
                //   Your Upload File Size :5 MB is Greater than upload_max_filesize :2M Or post_max_size :8M in php.ini
                $this->messageManager->addError("Your Upload File Size :" . $filesize . " is Greater than upload_max_filesize :" . $upload_max_size_def . " Or post_max_size :" . $post_max_size_def . " in php.ini");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

            /*Match extensions allowed*/
            $videoName = str_replace(' ', '_', $_FILES['videoimg_0']['name']);
            $ext = pathinfo($videoName, PATHINFO_EXTENSION);
            $allowedExtensions = ['flv', 'mp3', 'mp4', 'MPEG'];
            if (!in_array($ext, $allowedExtensions)) {
                $this->messageManager->addError("Only flv,mp3,mp4 and MPEG files allowed!");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }
            /*Upload video*/
            $path1 = $path . basename($videoName);
            if (move_uploaded_file($_FILES['videoimg_0']['tmp_name'], $path1)) {
                $data['video_file'] = '/productvideos/files/' . $time . $videoName;

            } else {
                $this->messageManager->addError("There was an error uploading the file, please try again!");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

        }

        if (isset($_FILES['videoimg_1']['name']) && $_FILES['videoimg_1']['name'] != '') {

            if ($_FILES['videoimg_1']['size'] > $post_max_size || $_FILES['videoimg_1']['size'] > $upload_max_size) {
                //   Your Upload File Size :5 MB is Greater than upload_max_filesize :2M Or post_max_size :8M in php.ini
                $this->messageManager->addError("Your Upload File Size :" . $filesize . " is Greater than upload_max_filesize :" . $upload_max_size_def . " Or post_max_size :" . $post_max_size_def . " in php.ini");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

            /*Match extensions allowed*/
            $imageName = str_replace(' ', '_', $_FILES['videoimg_1']['name']);
            $ext = pathinfo($imageName, PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'png', 'jpeg'];
            if (!in_array($ext, $allowedExtensions)) {
                $this->messageManager->addError("Only jpg,png,jpeg files allowed!");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }
            // echo move_uploaded_file($_FILES['videoimg_0']['tmp_name'], $path1);

            // exit;
            /*Upload image*/
            if ($_FILES['videoimg_1']['name']) {
                $imageName = str_replace(' ', '_', $_FILES['videoimg_1']['name']);
                $path2 = $path . basename($imageName);
                if (move_uploaded_file($_FILES['videoimg_1']['tmp_name'], $path2)) {
                    $data['video_thumb'] = '/productvideos/files/' . $time . $imageName;//exit;

                } else {
                    $this->messageManager->addError("There was an error uploading the file, please try again!");
                    return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
                }
            } else {
                $data['video_thumb'] = null;
            }
        }

        $data['video_type'] = 'file';
        $pro_array[] = $data['productSku'];

        $data['product_id'] = $pro_array;
        $data['store_id'] = 0;

        if (empty($data['video_id'])) {
            $data['video_id'] = null;
        }

        $model = $this->_objectManager->create('FME\Productvideos\Model\Productvideos');
        if ($id) {
            $model->load($id);
        }
        $model->setData($data);

        if ($id) {
            $model->setId($id);
        }

        try {
            if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                $model->setCreatedTime(date('y-m-d h:i:s'))->setUpdateTime(date('y-m-d h:i:s'));
            } else {
                $model->setUpdateTime(date('y-m-d h:i:s'));
            }
            $model->save();
            $this->messageManager->addSuccess(__('Video was successfully saved'));
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['video_id' => $model->getId()]);
                return;
            }
            return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            //return;
        } catch (\Exception $e) {
            //echo $e->getMessage();exit;
            $this->messageManager->addError($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
        }

    }

    public function parse_size($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * @param $data
     * @param $pro_array
     * @param $id
     */
    protected function addNewRecord($data, $pro_array, $id)
    {
        if (isset($_FILES['videoimg_0']['name']) && $_FILES['videoimg_0']['name'] != '') {

            /*Match get size*/
            $post_max_size = $this->parse_size(ini_get('post_max_size'));//echo "<br/>";
            $upload_max_size = $this->parse_size(ini_get('upload_max_filesize'));//echo "<br/>";
            $upload_max_size_def = ini_get('upload_max_filesize');//echo "<br/>";
            $post_max_size_def = ini_get('post_max_size');//echo "<br/>";
            $filesize = $this->formatSizeUnits($_FILES['videoimg_0']['size']);

            /*Match size allowed*/
            if ($_FILES['videoimg_0']['size'] > $post_max_size || $_FILES['videoimg_0']['size'] > $upload_max_size) {
                //   Your Upload File Size :5 MB is Greater than upload_max_filesize :2M Or post_max_size :8M in php.ini
                $this->messageManager->addError("Your Upload File Size :" . $filesize . " is Greater than upload_max_filesize :" . $upload_max_size_def . " Or post_max_size :" . $post_max_size_def . " in php.ini");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

            /*Match extensions allowed*/
            $videoName = str_replace(' ', '_', $_FILES['videoimg_0']['name']);
            $ext = pathinfo($videoName, PATHINFO_EXTENSION);
            $allowedExtensions = ['flv', 'mp3', 'mp4', 'MPEG'];
            if (!in_array($ext, $allowedExtensions)) {
                $this->messageManager->addError("Only flv,mp3,mp4 and MPEG files allowed!");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

            if ($_FILES['videoimg_1']['size'] > $post_max_size || $_FILES['videoimg_1']['size'] > $upload_max_size) {
                //   Your Upload File Size :5 MB is Greater than upload_max_filesize :2M Or post_max_size :8M in php.ini
                $this->messageManager->addError("Your Upload File Size :" . $filesize . " is Greater than upload_max_filesize :" . $upload_max_size_def . " Or post_max_size :" . $post_max_size_def . " in php.ini");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

            /*Match extensions allowed*/
            $imageName = str_replace(' ', '_', $_FILES['videoimg_1']['name']);
            $ext = pathinfo($imageName, PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'png', 'jpeg'];
            if (!in_array($ext, $allowedExtensions)) {
                $this->messageManager->addError("Only jpg,png,jpeg files allowed!");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

            /*Upload video*/
            $time = time() . '_';
            $path = $this->_objectManager->get('\Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'productvideos/files/' . $time;
            $path1 = $path . basename($videoName);


            if (move_uploaded_file($_FILES['videoimg_0']['tmp_name'], $path1)) {
                $data['video_file'] = '/productvideos/files/' . $time . $videoName;//exit;

            } else {
                $this->messageManager->addError("There was an error uploading the file, please try again!");
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

            // echo move_uploaded_file($_FILES['videoimg_0']['tmp_name'], $path1);

            // exit;
            /*Upload image*/
            if ($_FILES['videoimg_1']['name']) {
                $imageName = str_replace(' ', '_', $_FILES['videoimg_1']['name']);
                $path2 = $path . basename($imageName);
                if (move_uploaded_file($_FILES['videoimg_1']['tmp_name'], $path2)) {
                    $data['video_thumb'] = '/productvideos/files/' . $time . $imageName;//exit;
                } else {
                    $this->messageManager->addError("There was an error uploading the file, please try again!");
                    return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
                }
            } else {
                $data['video_thumb'] = null;
            }

            $data['video_type'] = 'file';
            $pro_array[] = $data['productSku'];

            $data['product_id'] = $pro_array;
            $data['store_id'] = 0;

            if (empty($data['video_id'])) {
                $data['video_id'] = null;
            }


            $model = $this->_objectManager->create('FME\Productvideos\Model\Productvideos');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);

            if ($id) {
                $model->setId($id);
            }

            try {
                if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                    $model->setCreatedTime(date('y-m-d h:i:s'))->setUpdateTime(date('y-m-d h:i:s'));
                } else {
                    $model->setUpdateTime(date('y-m-d h:i:s'));
                }
                $model->save();
                $this->messageManager->addSuccess(__('Video was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['video_id' => $model->getId()]);
                    return;
                }
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
                //return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);
            }

        } else {
            $this->messageManager->addError("Please upload video file!");
            return $this->resultRedirectFactory->create()->setPath('*/*/index', ['_secure' => $this->getRequest()->isSecure()]);

        }
    }

    /**
     * [_validateData to validate required fields and value of post data].
     *
     * @return [array] [return an array of errors if any]
     */
    private function _validateData($params)
    {
        $errors = [];
        $data = [];
        if (isset($params['img'])) {
            foreach ($params['img'] as $code => $value) {
                $filteredData = $this->_checkErrors($code, $value);
                if (isset($filteredData['data']) && $filteredData['data'] !== "") {
                    $data[$code] = $value;
                } elseif (isset($filteredData['error']) && $filteredData['error'] !== "") {
                    $errors[] = $filteredData['error'];
                }
            }
        }
        return [$data, $errors];
    }

    /**
     * [_checkErrors used to check for errors]
     *
     * @param  [integer] $code  [contains index]
     * @param  [string]  $value [contains parameter value]
     * @return [array]          [return data and error(s), if any]
     */
    private function _checkErrors($code, $value)
    {
        $data = '';
        $errors = '';
        switch ($code) :
            case 'width':
                if (trim($value) != '' && is_numeric($value)) {
                    $data = $value;
                } else {
                    $errors = __('Image width field can not contain any space,' . ' alphabet or special character');
                }
                break;
            case 'height':
                if (trim($value) != '' && is_numeric($value)) {
                    $data = $value;
                } else {
                    $errors = __('Image height field can not contain any space,' . ' alphabet or special character');
                }
                break;
            case 'speed':
                if (trim($value) != '' && is_numeric($value)) {
                    $data = $value;
                } else {
                    $errors = __('Video speed field can not contain any space,' . ' alphabet or special character');
                }
                break;
            case 'duration':
                if (trim($value) != '' && is_numeric($value)) {
                    $data = $value;
                } else {
                    $errors = __('Video duration field can not contain any space,' . ' alphabet or special character');
                }
        endswitch;

        return ['data' => $data, 'error' => $errors];
    }

    /**
     * [_uploadVideoImages used to upload images into directory]
     *
     * @param  [array]  $imageFiles [contains counter for files]
     * @param  [string] $target     [contains target to upload file]
     * @return [array]              [returns uploaded files names]
     */
    private function _uploadVideoImages($imageFiles, $target, $data)
    {
        $name = [];
        $i = 0;

        if (count($imageFiles) > 0 && isset($imageFiles[0]) && $imageFiles[0] !== "" && $imageFiles[0] !== null) {
            foreach ($imageFiles as $wkValue) {
                /**
                 * @var $uploader
                 * \Magento\MediaStorage\Model\File\Uploader
                 */
                //echo "ok12";exit;
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'videoimg_' . $wkValue]);

                $image = $uploader->validateFile();

                if (isset($image['tmp_name']) && $image['tmp_name'] !== '' && $image['tmp_name'] !== null) {
                    $imageCheck = getimagesize($image['tmp_name']);

                    if ($imageCheck['mime']) {
                        $image['name'] = str_replace(" ", "_", $image['name']);
                        $imgName = rand(1, 99999) . $image['name'];
                        $name[$i]['image'] = $imgName;
                        if (isset($data['videoimgurl_' . $wkValue])) {
                            $name[$i]['url'] = $data['videoimgurl_' . $wkValue];
                        } else {
                            $name[$i]['url'] = '#';
                        }

                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $uploader->setAllowRenameFiles(true);
                        $result = $uploader->save($target, $imgName);

                        if (isset($result['error']) && $result['error'] !== 0) {
                            $this->messageManager->addError(__('%1 Image Not Uploaded', $image['name']));
                            $this->_error = true;
                        } else {
                            $name[$i]['image'] = $result['file'];
                        }
                        $i++;
                    } else {
                        $this->messageManager->addError(__('Disallowed file type.'));
                        $this->_error = true;
                    }
                }
            }
        }
        return $name;
    }

    /**
     * [_saveVideoImageSettings used to save video image settings in DB]
     *
     * @param [array]   $imageDataArray [contains files names]
     * @param [string]  $videoImages   [used to implode files names into string]
     * @param [integer] $sellerId       [contains seller id]
     * @param [string]  $serial         [contains serialized data]
     */
    private function _saveVideoImageSettings($imageDataArray, $sellerId, $serial)
    {
        $tempImgArray = [];
        $countImage = count($imageDataArray);
        $videoImages = '';
        $i = 0;
        $collection = $this->sellerCollection->create()->addFieldToFilter('seller_id', $sellerId);

        if ($collection->getSize()) {
            foreach ($collection as $value) {
                $videoImage = $value->getVideoImg();
                $sellerData = $this->getVideoData($value->getEntityId());

                if (($videoImage == null || $videoImage == '') && $countImage > 0) {
                    $videoImages = serialize($imageDataArray);
                } else {
                    if (@unserialize($videoImage)) {
                        $existingVideoImages = unserialize($videoImage);
                    } else {
                        $existingVideoImages = explode(',', $videoImage);
                    }

                    $existingVideoImages = array_filter($existingVideoImages);

                    foreach ($existingVideoImages as $existImages) {
                        // if (!in_array($existImages, $imageDataArray)) {
                        //     array_push($imageDataArray, $existImages);
                        // }
                        if (is_array($existImages) && isset($existImages['image'])) {
                            $tempImgArray[$i]['image'] = $existImages['image'];
                            $tempImgArray[$i]['url'] = $existImages['url'];
                        } else {
                            $tempImgArray[$i]['image'] = $existImages;
                            $tempImgArray[$i]['url'] = '#';
                        }
                        $i++;
                    }
                    $imageDataArray = array_merge($imageDataArray, $tempImgArray);

                    if ($sellerData && $sellerData->getEntityId()) {
                        $videoImages = serialize($imageDataArray);
                    }
                }
                $this->saveVideoData($value->getEntityId(), $videoImages, $serial);
            }
            $this->messageManager->addSuccess(__('Gallery was successfully saved'));
        }
    }

    /**
     * [getVideoData used to load seller model]
     *
     * @param  [integer] $id [contains auto id of model]
     * @return [object]
     */
    public function getVideoData($id)
    {
        return $sellerData = $this->sellerModel->load($id);
    }

    /**
     * [saveVideoData used to save video data in model]
     *
     * @param [integer] $id           [description]
     * @param [string]  $videoImages [contains video images of seller]
     * @param [string]  $serial       [contains video settings]
     */
    public function saveVideoData($id, $videoImages, $serial)
    {
        $sellerData = $this->getVideoData($id);
        $sellerData->setVideoImg($videoImages);
        $sellerData->setVideoSetting($serial);
        $sellerData->save();
    }
}
