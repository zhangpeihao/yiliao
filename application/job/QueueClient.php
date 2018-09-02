<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/6
 * Time: 18:01
 */
namespace app\job;
use think\queue\Job;

class QueueClient
{
    /**
     * 邮件提醒
     * @param array $data  内容
     * @return
     */
    public function sendMAIL(Job $job, $data)
    {
        $isJobDone = $this->send($data);
        dump($isJobDone);
        if ($isJobDone) {
            //成功删除任务
            $job->delete();
        } else {
            //任务轮询4次后删除
            if ($job->attempts() > 3) {
                // 第1种处理方式：重新发布任务,该任务延迟10秒后再执行
                //$job->release(10);
                // 第2种处理方式：原任务的基础上1分钟执行一次并增加尝试次数
                //$job->failed();
                // 第3种处理方式：删除任务
                $job->delete();
            }
        }
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function send($data)
    {
        $sendemail = new Sendmail();
        $result    = $sendemail->sendMail($data);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}